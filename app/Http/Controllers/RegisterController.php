protected function validator(array $data)
{
    return Validator::make($data, [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        // Validasi Token Rahasia Instansi Siber
        'instance_token' => ['required', 'string', function ($attribute, $value, $fail) {
            if ($value !== 'SIBER-DISKOMINFO-2026') { // Ganti dengan token rahasia dinas Anda
                $fail('Token Validasi Instansi tidak sah! Pendaftaran diblokir.');
            }
        }],
    ]);
}

protected function create(array $data)
{
    return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'role' => 'Petugas', // Set otomatis sebagai petugas
    ]);
}