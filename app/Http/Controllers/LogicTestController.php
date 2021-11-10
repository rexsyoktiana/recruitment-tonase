<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogicTestController extends Controller
{
    public function index(int $id)
    {
        if (strlen($id) != 7) {
            $response = [
                'status'    =>  'error',
                'data'      =>  ['id' => $id],
                'message'   =>  'Digit id harus 7'
            ];
            return response()->json($response, 400);
        }

        $checkPrima = $this->checkPrima($id);
        if ($checkPrima) {
            // Kondisi tidak mengandung 0
            if (strpos($id, '0') === false) {
                $central = substr($id, -4);
                $checkPrima = $this->checkPrima($central);
                if ($checkPrima) {
                    $response = [
                        'status'    =>  'success',
                        'data'      =>  ['id' => $id, 'position' => 'Tengah'],
                        'message'   =>  'Berhasil menentukan posisi container'
                    ];
                    return response()->json($response, 200);
                }

                $right = substr($id, -3);
                if ($right[0] == $right[1] && $right[1] == $right[2]) {
                    $response = [
                        'status'    =>  'success',
                        'data'      =>  ['id' => $id, 'position' => 'Kanan'],
                        'message'   =>  'Berhasil menentukan posisi container'
                    ];
                    return response()->json($response, 200);
                }

                $left = substr($id, -2);
                $checkPrima = $this->checkPrima($left);
                if ($checkPrima) {
                    $checkBerurutan = $this->checkBerurutan($left[0], $left[1]);
                    if ($checkBerurutan) {
                        $response = [
                            'status'    =>  'success',
                            'data'      =>  ['id' => $id, 'position' => 'Kanan'],
                            'message'   =>  'Berhasil menentukan posisi container'
                        ];
                        return response()->json($response, 200);
                    }
                }
            }
        } else {
            // Kondisi mengandung 0
            if (strpos($id, '0') !== false) {
                $response = [
                    'status'    =>  'success',
                    'data'      =>  ['id' => $id, 'position' => 'Reject'],
                    'message'   =>  'Berhasil menentukan posisi container'
                ];
                return response()->json($response, 200);
            }
        }
        $response = [
            'status'    =>  'error',
            'data'      =>  ['id' => $id],
            'message'   =>  'Tidak ada posisi yang sesuai'
        ];
        return response()->json($response, 400);
    }

    public function checkPrima($num)
    {
        if ($num <= 10) {
            $prima = 0;
            for ($i = 1; $i <= $num; $i++) {
                if ($num % $i == 0) {
                    $prima++;
                }
            }
            if ($prima == 2) {
                return true;
            }
            return false;
        } else {
            if ($num % 2 == 0) {
                return false;
            } elseif ($num % 3 == 0) {
                return false;
            } elseif ($num % 5 == 0) {
                return false;
            } elseif ($num % 7 == 0) {
                return false;
            } elseif ($num % 9 == 0) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function checkBerurutan($first, $last)
    {
        if ($first + 1 == $last) {
            return true;
        }
        return false;
    }
}
