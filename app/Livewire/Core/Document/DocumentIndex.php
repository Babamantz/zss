<?php

namespace App\Livewire\Core\Document;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 15;
    public $sortField = 'created_at';
    public $sortDir = 'desc';

    public $confirmingDeletion = false;
    public $documentToDelete = null;

    protected $queryString = [
        'search'  => ['except' => ''],
        'perPage' => ['except' => 15],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->reset('search');
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
        $this->resetPage();
    }

    public function render()
    {
        $documents = Document::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        $stats = [
            'total_documents'      => Document::count(),
            'documents_this_month' => Document::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'documents_today'      => Document::whereDate('created_at', now()->toDateString())->count(),
        ];

        return view('livewire.core.document.document-index', [
            'documents' => $documents,
            'stats'     => $stats,
        ]);
    }
}
