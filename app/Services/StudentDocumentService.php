<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Exception;

class StudentDocumentService
{
    /**
     * Allowed MIME types for student documents.
     */
    private const ALLOWED_MIMES = [
        'application/pdf',
        'image/jpeg',
        'image/jpg',
        'image/png',
    ];

    /**
     * Upload (or replace) a document for a student.
     *
     * Storage path: students/{student_id}/{document_type}/{unique_filename}
     *
     * If a document of the same type already exists for this student,
     * the old file is deleted and the DB record is updated.
     *
     * @param Student      $student
     * @param UploadedFile $file
     * @param string       $documentType
     * @return StudentDocument
     * @throws Exception
     */
    public function uploadDocument(Student $student, UploadedFile $file, string $documentType): StudentDocument
    {
        // Validate MIME type
        if (!in_array($file->getMimeType(), self::ALLOWED_MIMES)) {
            throw new Exception(
                "Invalid file type [{$file->getMimeType()}]. Allowed: pdf, jpg, jpeg, png."
            );
        }

        // Build storage path
        $extension    = $file->getClientOriginalExtension();
        $uniqueName   = time() . '_' . uniqid() . '.' . $extension;
        $storagePath  = "students/{$student->id}/{$documentType}/{$uniqueName}";

        // Check if document of this type already exists for this student
        $existing = StudentDocument::where('student_id', $student->id)
            ->where('document_type', $documentType)
            ->first();

        if ($existing) {
            // Delete old physical file if it exists
            if (Storage::disk('public')->exists($existing->file_path)) {
                Storage::disk('public')->delete($existing->file_path);
            }

            // Store new file
            Storage::disk('public')->putFileAs(
                "students/{$student->id}/{$documentType}",
                $file,
                $uniqueName
            );

            // Update DB record
            $existing->update([
                'file_name'   => $file->getClientOriginalName(),
                'file_path'   => $storagePath,
                'file_size'   => $file->getSize(),
                'mime_type'   => $file->getMimeType(),
                'uploaded_by' => auth()->id(),
                'verified_by' => null,
                'verified_at' => null,
            ]);

            return $existing->fresh();
        }

        // Store new file
        Storage::disk('public')->putFileAs(
            "students/{$student->id}/{$documentType}",
            $file,
            $uniqueName
        );

        // Create DB record
        return StudentDocument::create([
            'student_id'    => $student->id,
            'document_type' => $documentType,
            'file_name'     => $file->getClientOriginalName(),
            'file_path'     => $storagePath,
            'file_size'     => $file->getSize(),
            'mime_type'     => $file->getMimeType(),
            'uploaded_by'   => auth()->id(),
        ]);
    }

    /**
     * Delete a student document — both physical file and DB record.
     *
     * @param StudentDocument $document
     * @return void
     */
    public function deleteDocument(StudentDocument $document): void
    {
        try {
            // Delete physical file
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Delete DB record
            $document->delete();
        } catch (Exception $e) {
            // Re-throw so calling code is aware of failure
            throw new Exception("Failed to delete document: " . $e->getMessage());
        }
    }
}
