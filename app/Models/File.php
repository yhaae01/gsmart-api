<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    const FILE_PNG = 'png';
    const FILE_JPG = 'jpg';
    const FILE_JPEG = 'jpeg';
    const FILE_XLSX = 'xlsx';
    const FILE_DOCX = 'docx';
    const FILE_DOC = 'doc';
    const FILE_PDF = 'pdf';
    const FILE_EML = 'eml';

    const CONTENT_TYPES = [
        self::FILE_PNG => 'image/png',
        self::FILE_JPG => 'image/jpg',
        self::FILE_JPEG => 'image/jpeg',
        self::FILE_XLSX => 'application/xlsx',
        self::FILE_DOCX => 'application/docx',
        self::FILE_DOC => 'application/doc',
        self::FILE_PDF => 'application/pdf',
        self::FILE_EML => 'application/eml',
    ];

    protected $fillable = [
        'sales_requirement_id',
        'path',
    ];

    protected $appends = [
        'file_name',
        'content_type',
        'full_path',
        'uploader_name',
        'requirement_level',
    ];

    public function getRequirementLevelAttribute()
    {
        return $this->salesRequirement->requirement->level_id;
    }

    public function getUploaderNameAttribute()
    {
        return $this->uploader->name;
    }

    public function getFileNameAttribute()
    {
        return Str::afterLast($this->path, '/');
    }

    public function getContentTypeAttribute()
    {
        $path = $this->path;
        $get_extension = explode('.', $path);
        $extension = strtolower($get_extension[1]);

        return self::CONTENT_TYPES[$extension];
    }

    public function getFullPathAttribute()
    {
        return Storage::disk('public')->url($this->path);
    }

    public function salesRequirement()
    {
        return $this->belongsTo(SalesRequirement::class, 'sales_requirement_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
