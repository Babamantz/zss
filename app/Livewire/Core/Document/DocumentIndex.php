<?php

namespace App\Livewire\Core\Document;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class DocumentIndex extends Component
{

    public $search = '';
    public $confirmingDeletion = false;
    public $documentToDelete = null;

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($documentId)
    {
        $this->documentToDelete = $documentId;
        $this->confirmingDeletion = true;
    }

    public function deleteDocument()
    {
        if ($this->documentToDelete) {
            $document = Document::find($this->documentToDelete);

            if ($document) {
                // Delete file from storage
                if (Storage::disk('public')->exists($document->document_path)) {
                    Storage::disk('public')->delete($document->document_path);
                }

                // Delete database record
                $document->delete();

                session()->flash('message', 'Document deleted successfully.');
            }
        }

        $this->confirmingDeletion = false;
        $this->documentToDelete = null;
    }
    public function render()
    {
        $documents = Document::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);
        return view('livewire.core.document.document-index', [
            'documents' => $documents
        ]);
    }
}
