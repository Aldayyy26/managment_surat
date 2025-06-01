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

    public function selectPlaceholdersForm($id)
    {
        $template = TemplateSurat::findOrFail($id);

        return view('surats.select_placeholders', ['template' => $template, 'placeholders' => $template->placeholders]);
    }

    public function selectPlaceholders(Request $request, $id)
    {
        $template = TemplateSurat::findOrFail($id);

        $input = $request->input('required_placeholders', []);

        $required_placeholders = [];

        foreach ($input as $key => $data) {
            if (!empty($data['required'])) {
                $required_placeholders[$key] = [
                    'label' => $data['label'] ?? $key,
                    'type' => $data['type'] ?? 'text',
                    'options' => isset($data['options']) ? array_map('trim', explode(',', $data['options'])) : [],
                ];
            }
        }

        $template->update([
            'required_placeholders' => json_encode($required_placeholders),
        ]);

        return redirect()->route('surats.create')->with('success', 'Template surat berhasil disimpan dengan placeholder wajib.');
    }


    // --- Fungsi pembantu untuk scan placeholder sama seperti yang kamu punya ---
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
        preg_match_all('/\{\{(.*?)\}\}/', $content, $matches);

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

    public function edit(TemplateSurat $template)
    {
        return view('surats.edit', compact('template'));
    }

    public function update(Request $request, TemplateSurat $template)
    {
        $request->validate([
            'nama_surat' => 'required|string',
            'user_type' => 'required|in:mahasiswa,dosen',
        ]);

        $template->update([
            'nama_surat' => $request->nama_surat,
            'user_type' => $request->user_type,
        ]);

        return redirect()->route('surats.index')->with('success', 'Template diperbarui.');
    }

    public function destroy(TemplateSurat $template)
    {
        $template->delete();

        return back()->with('success', 'Template berhasil dihapus.');
    }
}
