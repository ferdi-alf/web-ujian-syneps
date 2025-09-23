<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Batches;
use App\Models\Kelas;
use App\Models\Materi;
use Illuminate\Console\View\Components\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class MateriController extends Controller
{
    public function index() {
        $role = Auth::user()->role;
         switch ($role) {
            case 'siswa':
                # code...
                break;
            
            default:
                $user = Auth::user();
                $kelas = collect();

            if ($user->role === 'admin') {
                    $kelas = Kelas::select('id', 'nama', 'type')->get();
                    $materi = Materi::with('kelas')->latest()->get();

                } elseif ($user->role === 'pengajar') {
                    $pengajarDetail = $user->pengajarDetail;
                    $kelasIds = $pengajarDetail ? $pengajarDetail->kelas()->pluck('kelas.id')->toArray() : [];
                    $kelas = Kelas::select('id', 'nama', 'type')->whereIn('id', $kelasIds)->get();
                         $materi = Materi::with('kelas')
                            ->latest()
                            ->where('kelas_id', $pengajarDetail->kelas_id)
                            ->get();

                }
            break;
         }
        return view('Dashboard.Materi', compact('kelas', 'materi'));
    }

    public function show($id)
    {
        try {
            $materi = Materi::with(['kelas', 'batch'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $materi->id,
                    'judul' => $materi->judul,
                    'kelas_id' => $materi->kelas_id,
                    'file_pdf' => $materi->file_pdf,
                    'file_pdf_name' => $materi->file_pdf ? basename($materi->file_pdf) : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function showPdf($id) {
        try {
            $user = Auth::user();
            $materi = Materi::with('kelas', 'batch')->findOrFail($id);
            
            if(!$this->canAccessMateri($user, $materi)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki Akses ke materi ini'
                ], 403);
            }

            return response()->json([
                'success' => true,  
                'data' => [       
                    'id' => $materi->id,
                    'judul' => $materi->judul,
                    'kelas_id' => $materi->kelas_id,
                    'batch_id' => $materi->batch_id,
                    'file_pdf' => $materi->materi,
                    'file_pdf_name' => $materi->materi ? basename($materi->materi) : null,
                    'file_pdf_url' => $materi->materi ? Storage::url($materi->materi) : null,
                    'kelas' => $materi->kelas,
                    'batch' => $materi->batch,
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan ' . $th->getMessage()
            ], 404);
        }
    }

    private function canAccessMateri($user, $materi) {
        switch ($user->role) {
            case 'admin':
                return true;
                break;
            case 'siswa':
                $siswaDetail = $user->siswaDetail;
                if (!$siswaDetail) return false;

                return $siswaDetail->kelas_id == $materi->kelas_id &&
                       $siswaDetail->batch_id == $materi->batch_id;
                break;
            case 'pengajar':
                $pengajarDetail = $user->pengajarDetail;
                if(!$pengajarDetail) return false;

                return $pengajarDetail->kelas()->where('kelas_id', $materi->kelas_id)->exists();
                break;
            default:
                return false;
                break;
        }
    } 

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'judul' => 'required|string|max:255',
            'file_pdf' => 'required|file|mimes:pdf|max:10240', // max 10MB
            'kelas_id' => 'required|exists:kelas,id',
        ], [
            'judul.required' => 'Judul materi wajib diisi',
            'file_pdf.required' => 'File PDF wajib diupload',
            'file_pdf.mimes' => 'File harus berformat PDF',
            'file_pdf.max' => 'Ukuran file maksimal 10MB',
            'kelas_id.required' => 'Kelas wajib dipilih',
            'kelas_id.exists' => 'Kelas yang dipilih tidak valid',
        ]);

        try {
            // Cari batch yang aktif untuk kelas yang dipilih
            $activeBatch = Batches::where('kelas_id', $request->kelas_id)
                ->where('status', 'active')
                ->first();

            if (!$activeBatch) {
                return redirect()->back()
                    ->with(AlertHelper::error('Tidak ada batch aktif untuk kelas yang dipilih', 'Error'))
                    ->withInput();
            }

            $filePath = null;
            
            if ($request->hasFile('file_pdf')) {
                $file = $request->file('file_pdf');
                $fileName = time() . '_' . Str::slug($request->judul) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('materi', $fileName, 'public');
            }

            Materi::create([
                'judul' => $request->judul,
                'materi' => $filePath,
                'kelas_id' => $request->kelas_id,
                'batch_id' => $activeBatch->id,
            ]);

            return redirect()->route('materi.index')
                ->with(AlertHelper::success('Materi berhasil ditambahkan!', 'Success'));
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with(AlertHelper::error('Gagal menambahkan materi: ' . $e->getMessage(), 'Error'))
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $materi = Materi::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'file_pdf' => 'nullable|file|mimes:pdf|max:10240', // max 10MB
            'kelas_id' => 'required|exists:kelas,id',
        ], [
            'judul.required' => 'Judul materi wajib diisi',
            'file_pdf.mimes' => 'File harus berformat PDF',
            'file_pdf.max' => 'Ukuran file maksimal 10MB',
            'kelas_id.required' => 'Kelas wajib dipilih',
            'kelas_id.exists' => 'Kelas yang dipilih tidak valid',
        ]);

        try {
            // Cari batch yang aktif untuk kelas yang dipilih
            $activeBatch = Batches::where('kelas_id', $request->kelas_id)
                ->where('status', 'active')
                ->first();

            if (!$activeBatch) {
                return redirect()->back()
                    ->with(AlertHelper::error('Tidak ada batch aktif untuk kelas yang dipilih', 'Error'))
                    ->withInput();
            }

            $filePath = $materi->materi; // Gunakan kolom 'materi' sesuai fillable
            
            if ($request->hasFile('file_pdf')) {
                if ($materi->materi && Storage::disk('public')->exists($materi->materi)) {
                    Storage::disk('public')->delete($materi->materi);
                }
                
                $file = $request->file('file_pdf');
                $fileName = time() . '_' . Str::slug($request->judul) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('materi', $fileName, 'public');
            }

            $materi->update([
                'judul' => $request->judul,
                'materi' => $filePath,
                'kelas_id' => $request->kelas_id,
                'batch_id' => $activeBatch->id,
            ]);

            return redirect()->route('materi.index')
                ->with(AlertHelper::success('Data materi berhasil diperbarui', 'Success'));
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with(AlertHelper::error('Gagal memperbarui materi: ' . $e->getMessage(), 'Error'))
                ->withInput();
        }
    }


    public function destroy($id)
    {
        try {
            $materi = Materi::findOrFail($id);
            
            if ($materi->file_pdf && Storage::disk('public')->exists($materi->file_pdf)) {
                Storage::disk('public')->delete($materi->file_pdf);
            }
            
            $materi->delete();

            return back()->with(AlertHelper::success('Materi berhasil dihapus!', 'Success'));
                
        } catch (\Exception $e) {
           return back()->with(AlertHelper::error('Gagal menghapus materi: ' . $e->getMessage(), 'Error'));
        }
    }

    public function download($id)
    {
        $materi = Materi::findOrFail($id);

        if (!$materi->materi) {
            return redirect()->back()
                ->with(AlertHelper::error('File tidak tersedia!', 'Error'));
        }

        $fullPath = storage_path('app/public/' . $materi->materi);

        if (!file_exists($fullPath)) {
            return redirect()->back()
                ->with(AlertHelper::error('File tidak ditemukan di server!', 'Error'));
        }

        try {
            $downloadName = Str::slug($materi->judul) . '.pdf';
            
            return response()->download($fullPath, $downloadName, [
                'Content-Type' => 'application/pdf',
            ]);
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with(AlertHelper::error('Gagal mendownload file: ' . $e->getMessage(), 'Error'));
        }
    }
}