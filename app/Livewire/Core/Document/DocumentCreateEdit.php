<?php

namespace App\Livewire\Core\Document;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentCreateEdit extends Component
{

    use WithFileUploads;

    public $documentId;
    public $name;
    public $document;
    public $existingDocumentPath;
    public $isEditMode = false;
    public $isViewMode = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'document' => $this->isEditMode ? 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240' : 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ];
    }

    public function mount($documentId = null, $mode = null)
    {
        $this->documentId = $documentId;
        $this->isEditMode = !is_null($documentId);
        $this->isViewMode = ($mode === 'view');

        if ($documentId) {
            $this->loadDocument();
        }
    }

    public function loadDocument()
    {
        $document = Document::findOrFail($this->documentId);
        $this->name = $document->name;
        $this->existingDocumentPath = $document->document_path;
    }

    public function submitForm()
    {
        $this->validate();

        try {
            $documentPath = $this->existingDocumentPath;

            // Handle file upload
            if ($this->document) {
                // Delete old file if editing
                if ($this->isEditMode && $this->existingDocumentPath) {
                    if (Storage::disk('public')->exists($this->existingDocumentPath)) {
                        Storage::disk('public')->delete($this->existingDocumentPath);
                    }
                }

                // Store new file
                $documentPath = $this->document->store('documents', 'public');
            }

            // Create or update document
            if ($this->isEditMode) {
                $document = Document::findOrFail($this->documentId);
                $document->update([
                    'name' => $this->name,
                    'document_path' => $documentPath,
                ]);
                session()->flash('message', 'Document updated successfully.');
            } else {
                Document::create([
                    'name' => $this->name,
                    'document_path' => $documentPath,
                ]);
                session()->flash('message', 'Document created successfully.');
            }

            return redirect()->route('documents.index');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function downloadDocument()
    {
        if ($this->existingDocumentPath && Storage::disk('public')->exists($this->existingDocumentPath)) {
            return Storage::disk('public')->download($this->existingDocumentPath);
        }

        session()->flash('error', 'Document not found.');
    }
    public function render()
    {
        return view('livewire.core.document.document-create-edit');
    }
}
