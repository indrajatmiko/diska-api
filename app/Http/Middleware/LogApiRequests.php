<?php
namespace App\Http\Middleware;
use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;

class LogApiRequests
{
    public function handle(Request $request, Closure $next)
    {
        // Catat waktu mulai
        $startTime = microtime(true);

        // Lanjutkan ke controller
        $response = $next($request);

        // Hitung durasi setelah mendapatkan respons
        $duration = (microtime(true) - $startTime) * 1000;

        // Simpan log ke database
        ApiLog::create([
            'user_id' => $request->user()?->id,
            'ip_address' => $request->ip(),
            'method' => $request->method(),
            'route' => $request->route()?->uri() ?? $request->path(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => (int) $duration,
            'created_at' => now(),
        ]);

        return $response;
    }
}