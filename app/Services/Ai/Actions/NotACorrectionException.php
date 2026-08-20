<?php

namespace App\Services\Ai\Actions;

use RuntimeException;

/**
 * Ditandai saat balasan user pada sebuah aksi yang menunggu konfirmasi
 * ternyata BUKAN koreksi, melainkan permintaan baru. Bot lalu membuang aksi
 * tertunda dan memproses pesan itu seperti biasa.
 */
class NotACorrectionException extends RuntimeException
{
}
