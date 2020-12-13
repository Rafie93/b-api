<?php
use Carbon\Carbon;
// use App\Models\Sistems\LogActivity;
use App\Models\Sistem\NumberSequence;

 function generateCode($name,$c="SO")
{
    $tahun = date('Y');
    $bulan = monthToRomawi(date('m'));
    $numberData = NumberSequence::where('seq_name',$name)
                                ->where('seq_year',$tahun)
                                ->first();
    $number = "1";
    if($numberData){
        $number = $numberData->seq_value+1;
        NumberSequence::where('seq_name',$name)
        ->where('seq_year',$tahun)
        ->update([
            'seq_value'=>$number
        ]);
    }else{
        NumberSequence::insert([
            'seq_value'=>$number,
            'seq_year'=>$tahun,
            'seq_name'=>$name
        ]);
    }

    if($number<10){
        $number = "00".$number;
    }elseif($number<100){
        $number = "0".$number;
    }
    return $number.'-'.$c.'/'.$name.'-'.$bulan.'-'.$tahun;
}

function monthToRomawi($mounth)
{
    switch ($mounth) {
        case 1:
           return "I";
            break;
        case 2:
           return "II";
            break;
        case 3:
            return "III";
            break;
        case 4:
            return "IV";
            break;
        case 5:
            return "V";
            break;
        case 6:
            return "VI";
            break;
        case 7:
            return "VII";
            break;
        case 8:
            return "VIII";
            break;
        case 9:
            return "IX";
            break;
        case 10:
            return "X";
            break;
        case 11:
            return "XI";
            break;
        case 12:
            return "XII";
            break;
        default:
            return "I";
            break;
    }
}
function penyebut($nilai) {
    $nilai = abs($nilai);
    $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if ($nilai < 12) {
        $temp = " ". $huruf[$nilai];
    } else if ($nilai <20) {
        $temp = penyebut($nilai - 10). " belas";
    } else if ($nilai < 100) {
        $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
    } else if ($nilai < 200) {
        $temp = " seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
        $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = " seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
        $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
        $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
    } else if ($nilai < 1000000000000000) {
        $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
    }
    return $temp;
}
function terbilang($nilai) {
	if($nilai<0) {
		$hasil = "minus ". trim(penyebut($nilai));
	} else {
		$hasil = trim(penyebut($nilai));
	}
	return $hasil." Rupiah";
}
function replaceDate($date)
{
    return date("Y-m-d H:i:s", strtotime($date));
}


function ganti_format_tgl_indo($tgl = "")
{
	$tanggal = explode("-", $tgl);
	$tgl = $tanggal[2]." ".char_to_month($tanggal[1])." ".$tanggal[0];
	return $tgl;
}

function char_to_month($month)
{
	switch ($month)
	{
		case "01" : return "Januari";
		case "02" : return "Februari";
		case "03" : return "Maret";
		case "04" : return "April";
		case "05" : return "Mei";
		case "06" : return "Juni";
		case "07" : return "Juli";
		case "08" : return "Agustus";
		case "09" : return "September";
		case "10" : return "Oktober";
		case "11" : return "Nopember";
		case "12" : return "Desember";

		default : return FALSE;
	}
}



?>
