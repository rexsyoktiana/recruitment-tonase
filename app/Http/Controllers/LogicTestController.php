<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogicTestController extends Controller
{
    public function index(int $id)
    {
        if (strlen($id) != 7) {
            echo "digit id harus 7";
        }

        $checkPrima = $this->checkPrima($id);
        if ($checkPrima) {
            // Kondisi tidak mengandung 0
            if (strpos($id, '0') === false) {
                $central = substr($id, -4);
                $checkPrima = $this->checkPrima($central);
                if ($checkPrima) {
                    echo "Tengah";
                }

                $right = substr($id, -3);
                if ($right[0] == $right[1] && $right[1] == $right[2]) {
                    echo "Kanan";
                }

                $left = substr($id, -2);
                $checkPrima = $this->checkPrima($left);
                if ($checkPrima) {
                    $checkBerurutan = $this->checkBerurutan($left[0], $left[1]);
                    if ($checkBerurutan) {
                        echo "left";
                    }
                }
            }
        } else {
            // Kondisi mengandung 0
            if (strpos($id, '0') !== false) {
                echo "Reject";
            }else{
                echo "Dead";
            }
        }
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
