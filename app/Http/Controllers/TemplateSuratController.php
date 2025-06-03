<?php

namespace App\Http\Controllers;

use App\Models\TemplateSurat;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Row;
use PhpOffice\PhpWord\Element\Cell;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;

class TemplateSuratController extends Controller
{
    public function index(Request $request)
    {
        $query = TemplateSurat::query();

        if ($search = $request->get('search')) {
            $query->where('nama_surat', 'like', "%$search%");
        }

        $surats = $query->latest()->paginate(10);

        return view('surats.index', compact('surats'));
    }

    public function create()
    {
        $userRoles = Role::whereIn('name', ['mahasiswa', 'dosen'])->pluck('name');

        return view('surats.create', compact('userRoles'));
    }

    public function upload(Request $request)
    {
        $allowedRoles = Role::whereIn('name', ['mahasiswa', 'dosen'])->pluck('name')->toArray();

        $request->validate([
            'nama_surat' => 'required|string',
            'user_type' => ['required', Rule::in($allowedRoles)],
            'file_surat' => 'required|file|mimes:docx',
        ]);

        $file = $request->file('file_surat');
        $path = $file->store('surat_templates', 'public');
        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath)) {
            return back()->with('error', 'File gagal ditemukan setelah upload.');
        }

        try {
            $placeholders = $this->scanPlaceholders($fullPath);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file Word: ' . $e->getMessage());
        }

        // Simpan dulu template dengan placeholders dan required_placeholders kosong
        $template = TemplateSurat::create([
            'nama_surat' => $request->nama_surat,
            'file_path' => $path,
            'placeholders' => $placeholders,
            'required_placeholders' => json_encode([]),
            'user_type' => $request->user_type,
        ]);

        // Redirect ke halaman pilih placeholder dengan id template
        return redirect()->route('surats.selectPlaceholdersForm', $template->id)
            ->with('success', 'File berhasil diupload, silakan pilih placeholder yang wajib diisi.');
    }

    
public function selectPlaceholders(Request $request, $id)
    {
        $template = TemplateSurat::findOrFail($id);

        $inputPlaceholders = $request->input('required_placeholders', []);

        $cleanedPlaceholders = [];
        foreach ($inputPlaceholders as $key => $config) {
            if (!isset($config['can_input']) || $config['can_input'] != 1) {
                continue; // Skip jika tidak dicentang "bisa diajukan user"
            }

            $keyClean = trim($key);
            $cleanedPlaceholders[$keyClean] = [
                'required' => true,
                'nullable' => isset($config['nullable']) && $config['nullable'] == 1,
                'label' => trim($config['label'] ?? ''),
                'type' => $config['type'] ?? 'text',
                'options' => isset($config['options']) 
                    ? array_map('trim', explode(',', $config['options'])) 
                    : [],
            ];
        }

        $template->required_placeholders = json_encode($cleanedPlaceholders);
        $template->save();

        return redirect()->route('surats.index')
            ->with('success', 'Placeholder berhasil disimpan.');
    }

    public function selectPlaceholdersForm($id)
    {
        $template = TemplateSurat::findOrFail($id);

        // Decode placeholders dari file Word
        $placeholders = is_array($template->placeholders)
            ? $template->placeholders
            : json_decode($template->placeholders, true) ?? [];

        // Placeholder yang sudah dipilih sebelumnya
        $requiredPlaceholders = is_array($template->required_placeholders)
            ? $template->required_placeholders
            : json_decode($template->required_placeholders, true) ?? [];

        return view('surats.select_placeholders', [
            'template' => $template,
            'placeholders' => $placeholders,
            'existingPlaceholders' => $requiredPlaceholders,
        ]);
    }


    protected function scanPlaceholders($filePath)
    {
        $phpWord = IOFactory::load($filePath);
        $texts = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $texts = array_merge($texts, $this->extractTextFromElement($element));
            }
        }

        $content = implode(' ', $texts);

        // Bersihkan spasi di dalam placeholder supaya sesuai format ${...}
        $content = preg_replace('/\$\s*\{\s*(.*?)\s*\}/', '${$1}', $content);

        // Sekarang cari placeholder yang sudah rapi
        preg_match_all('/\$\{(.*?)\}/', $content, $matches);

        return array_unique($matches[1]);
    }



    protected function extractTextFromElement($element)
    {
        $texts = [];

        if ($element instanceof Text) {
            $texts[] = $element->getText();
        }

        if ($element instanceof TextRun) {
            foreach ($element->getElements() as $child) {
                $texts = array_merge($texts, $this->extractTextFromElement($child));
            }
        }

        if ($element instanceof Table) {
            foreach ($element->getRows() as $row) {
                if ($row instanceof Row) {
                    foreach ($row->getCells() as $cell) {
                        if ($cell instanceof Cell) {
                            foreach ($cell->getElements() as $cellElement) {
                                $texts = array_merge($texts, $this->extractTextFromElement($cellElement));
                            }
                        }
                    }
                }
            }
        }

        return $texts;
    }

    public function edit($id)
    {
        $template = TemplateSurat::findOrFail($id);

        // Ambil placeholders & required_placeholders
        $placeholders = is_array($template->placeholders)
            ? $template->placeholders
            : json_decode($template->placeholders, true) ?? [];

        $requiredPlaceholders = is_array($template->required_placeholders)
            ? $template->required_placeholders
            : json_decode($template->required_placeholders, true) ?? [];

        // Trim semua nilai dalam array placeholders
        $placeholders = array_map('trim', $placeholders);

        // Trim key dari requiredPlaceholders
        $requiredPlaceholdersTrimmed = [];
        foreach ($requiredPlaceholders as $key => $value) {
            $requiredPlaceholdersTrimmed[trim($key)] = $value;
        }

        return view('surats.edit', [
            'template' => $template,
            'placeholders' => $placeholders,
            'existingPlaceholders' => $requiredPlaceholdersTrimmed,
        ]);
    }

    public function update(Request $request, $id)
    {
        $template = TemplateSurat::findOrFail($id);

        $request->validate([
            'nama_surat' => 'required|string|max:255',
            'user_type' => 'required|in:mahasiswa,dosen',
        ]);

        $inputPlaceholders = $request->input('required_placeholders', []);

        $requiredPlaceholders = [];
        $allPlaceholders = [];

        foreach ($inputPlaceholders as $key => $config) {
            $keyClean = trim($key);

            $placeholderData = [
                'label' => trim($config['label'] ?? ''),
                'type' => $config['type'] ?? 'text',
                'options' => isset($config['options']) 
                    ? array_map('trim', explode(',', $config['options'])) 
                    : [],
            ];

            // Masukkan semua placeholder ke daftar umum
            $allPlaceholders[] = $keyClean;

            // Hanya masukkan ke required_placeholders jika bisa diajukan user
            if (isset($config['can_input']) && $config['can_input'] == 1) {
                $requiredPlaceholders[$keyClean] = array_merge($placeholderData, [
                    'required' => true,
                    'nullable' => isset($config['nullable']) && $config['nullable'] == 1,
                ]);
            }
        }

        $template->nama_surat = $request->nama_surat;
        $template->user_type = $request->user_type;
        $template->required_placeholders = json_encode($requiredPlaceholders);
        $template->placeholders = json_encode($allPlaceholders); // hanya simpan array key-nya

        $template->save();

        return redirect()->route('surats.edit', $template->id)
            ->with('success', 'Template berhasil diperbarui.');
    }


    public function destroy(TemplateSurat $template)
    {
        $template->delete();

        return back()->with('success', 'Template berhasil dihapus.');
    }
}
