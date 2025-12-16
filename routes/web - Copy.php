Route::get('/db-test', function () {
    try {
        \DB::connection()->getPdo();
        return "Koneksi DB OK: " . \DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});