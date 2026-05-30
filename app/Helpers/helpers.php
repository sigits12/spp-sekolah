<?php

if (!function_exists('fake_delay')) {
    function fake_delay($ms = 500)
    {
        sleep(2); // delay 2 detik

        return response()->json([
            'status' => 'success',
            'message' => 'Pembayaran berhasil'
        ]);
    }
}