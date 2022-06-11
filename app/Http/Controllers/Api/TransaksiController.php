<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Validator;
use Illuminate\Support\Str;
use Carbon\Carbon; //library time
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(){
        $transaksi = Transaksi::all();

        if(count($transaksi)>0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $transaksi
            ], 200);
        }//return semua data

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); //data empty
    }

    public function show(Request $request, $idTransaksi){
        $transaksi = Transaksi::where('idTransaksi' , '=', $idTransaksi)->first(); // mencari data berdasarkan id

        if(!is_null($transaksi)){
            return response([
                'message' => 'Retrieve Transaksi Success',
                'data' => $transaksi
            ], 200);// Found
        }

        return response([
            'message' => 'Transaksi Not Found',
            'data' => null
        ], 400);// not Found
    }

    public function showTransaksiJoinLengkap(Request $request, $idTransaksi){
        $transaksi = Transaksi::leftJoin('pembayarans', 'pembayarans.idPembayaran', '=', 'transaksis.idPembayaran')
                    ->leftJoin('users', 'users.idCustomer', '=', 'transaksis.idCustomer')
                    ->leftJoin('drivers', 'drivers.idDriver', '=', 'transaksis.idDriver')
                    ->leftJoin('pegawais', 'pegawais.idPegawai', '=', 'transaksis.idPegawai')
                    ->leftJoin('mobils', 'mobils.idMobil', '=', 'pembayarans.idMobil')
                    ->leftJoin('promos', 'promos.idPromo', '=', 'pembayarans.idPromo')
                    ->where('transaksis.idTransaksi', '=', $idTransaksi)
                    ->first(); // mencari data berdasarkan id

        $diff = Transaksi::selectRaw("DATEDIFF(transaksis.tanggalWaktuSelesai, transaksis.tanggalWaktuSewa) as diff")
        ->where('transaksis.idTransaksi', '=', $idTransaksi)
        ->first();

        if(!is_null($transaksi)){
            return response([
                'message' => 'Retrieve Transaksi Success',
                'data' => $transaksi,
                'diff' => $diff,
            ], 200);// Found
        }

        return response([
            'message' => 'Transaksi Not Found',
            'data' => null
        ], 400);// not Found
    }

    public function showTransaksiJoinLengkapByCustomer(Request $request, $idCustomer){
        $transaksi = Transaksi::leftJoin('pembayarans', 'pembayarans.idPembayaran', '=', 'transaksis.idPembayaran')
                    ->leftJoin('users', 'users.idCustomer', '=', 'transaksis.idCustomer')
                    ->leftJoin('drivers', 'drivers.idDriver', '=', 'transaksis.idDriver')
                    ->leftJoin('pegawais', 'pegawais.idPegawai', '=', 'transaksis.idPegawai')
                    ->leftJoin('mobils', 'mobils.idMobil', '=', 'pembayarans.idMobil')
                    ->leftJoin('promos', 'promos.idPromo', '=', 'pembayarans.idPromo')
                    ->where('transaksis.idCustomer', '=', $idCustomer)
                    ->get(); // mencari data berdasarkan id

        if(count($transaksi)>0){
            return response([
                'message' => 'Retrieve Transaksi Success',
                'data' => $transaksi,
            ], 200);// Found
        }

        return response([
            'message' => 'Transaksi Not Found',
            'data' => null
        ], 400);// not Found
    }

    public function showTransaksiJoinLengkapByDriver(Request $request, $idDriver){
        $transaksi = Transaksi::leftJoin('pembayarans', 'pembayarans.idPembayaran', '=', 'transaksis.idPembayaran')
                    ->leftJoin('users', 'users.idCustomer', '=', 'transaksis.idCustomer')
                    ->leftJoin('drivers', 'drivers.idDriver', '=', 'transaksis.idDriver')
                    ->leftJoin('pegawais', 'pegawais.idPegawai', '=', 'transaksis.idPegawai')
                    ->leftJoin('mobils', 'mobils.idMobil', '=', 'pembayarans.idMobil')
                    ->leftJoin('promos', 'promos.idPromo', '=', 'pembayarans.idPromo')
                    ->where('transaksis.idDriver', '=', $idDriver)
                    ->get(); // mencari data berdasarkan id

        if(count($transaksi)>0){
            return response([
                'message' => 'Retrieve Transaksi Success',
                'data' => $transaksi,
            ], 200);// Found
        }

        return response([
            'message' => 'Transaksi Not Found',
            'data' => null
        ], 400);// not Found
    }

    public function showTransaksiInProgress(Request $request, $idCustomer){
        $transaksi = Transaksi::where('idCustomer' , '=', $idCustomer)
                    ->join('pembayarans', 'pembayarans.idPembayaran', '=', 'transaksis.idPembayaran')
                    ->where('statusTransaksi', '=', 'Peminjaman Berlangsung')
                    ->orWhere('statusTransaksi', '=', 'Menunggu Konfirmasi')
                    ->orWhere('statusTransaksi', '=', 'Diterima')
                    ->orWhere('statusTransaksi', '=', 'Ditolak')
                    ->first(); // mencari data berdasarkan id

        if(!is_null($transaksi)){
            return response([
                'message' => 'Retrieve Transaksi Success',
                'data' => $transaksi
            ], 200);// Found
        }

        return response([
            'message' => 'Transaksi Not Found',
            'data' => null
        ], 200);// not Found
    }

    public function showTransaksiMenungguKonfirmasi(){
        $transaksi = Transaksi::leftJoin('pembayarans', 'pembayarans.idPembayaran', '=', 'transaksis.idPembayaran')
                    ->leftJoin('users', 'users.idCustomer', '=', 'transaksis.idCustomer')
                    ->leftJoin('drivers', 'drivers.idDriver', '=', 'transaksis.idDriver')
                    ->leftJoin('mobils', 'mobils.idMobil', '=', 'pembayarans.idMobil')
                    ->leftJoin('promos', 'promos.idPromo', '=', 'pembayarans.idPromo')
                    ->where('statusTransaksi', '=', 'Menunggu Konfirmasi')
                    ->get(); // mencari data berdasarkan id

        if(count($transaksi)>0){
            return response([
                'message' => 'Retrieve Transaksi Success',
                'data' => $transaksi
            ], 200);// Found
        }

        return response([
            'message' => 'Transaksi Not Found',
            'data' => null
        ], 200);// not Found
    }

    public function showTransaksiForCS($status){
        $transaksi = Transaksi::leftJoin('pembayarans', 'pembayarans.idPembayaran', '=', 'transaksis.idPembayaran')
                    ->leftJoin('users', 'users.idCustomer', '=', 'transaksis.idCustomer')
                    ->leftJoin('drivers', 'drivers.idDriver', '=', 'transaksis.idDriver')
                    ->leftJoin('pegawais', 'pegawais.idPegawai', '=', 'transaksis.idPegawai')
                    ->leftJoin('mobils', 'mobils.idMobil', '=', 'pembayarans.idMobil')
                    ->leftJoin('promos', 'promos.idPromo', '=', 'pembayarans.idPromo')
                    ->where('statusTransaksi', '=', $status)
                    ->get(); // mencari data berdasarkan id

        if(count($transaksi)>0){
            return response([
                'message' => 'Retrieve Transaksi Success',
                'data' => $transaksi
            ], 200);// Found
        }

        return response([
            'message' => 'Transaksi Not Found',
            'data' => null
        ], 200);// not Found
    }

    public function showUbahTransaksiCustomer( $idCustomer){
        $transaksi = Transaksi::leftJoin('pembayarans', 'pembayarans.idPembayaran', '=', 'transaksis.idPembayaran')
                    ->leftJoin('users', 'users.idCustomer', '=', 'transaksis.idCustomer')
                    ->leftJoin('drivers', 'drivers.idDriver', '=', 'transaksis.idDriver')
                    ->leftJoin('pegawais', 'pegawais.idPegawai', '=', 'transaksis.idPegawai')
                    ->leftJoin('mobils', 'mobils.idMobil', '=', 'pembayarans.idMobil')
                    ->leftJoin('promos', 'promos.idPromo', '=', 'pembayarans.idPromo')
                    ->where('statusTransaksi', '=', 'Menunggu Konfirmasi')
                    ->where('transaksis.idCustomer', '=', $idCustomer)
                    ->first(); // mencari data berdasarkan id

        if(!is_null($transaksi)){
            return response([
                'message' => 'Retrieve Transaksi Success',
                'data' => $transaksi
            ], 200);// Found
        }

        return response([
            'message' => 'Transaksi Not Found',
            'data' => null
        ], 200);// not Found
    }

    public function showTransaksiDitolak(){
        $transaksi = Transaksi::leftJoin('pembayarans', 'pembayarans.idPembayaran', '=', 'transaksis.idPembayaran')
                    ->leftJoin('users', 'users.idCustomer', '=', 'transaksis.idCustomer')
                    ->leftJoin('drivers', 'drivers.idDriver', '=', 'transaksis.idDriver')
                    ->leftJoin('pegawais', 'pegawais.idPegawai', '=', 'transaksis.idPegawai')
                    ->leftJoin('mobils', 'mobils.idMobil', '=', 'pembayarans.idMobil')
                    ->leftJoin('promos', 'promos.idPromo', '=', 'pembayarans.idPromo')
                    ->where('statusTransaksi', '=', 'Ditolak')
                    ->orWhere('statusTransaksi', '=', "Transaksi Ditolak")
                    ->get(); // mencari data berdasarkan id

        if(count($transaksi)>0){
            return response([
                'message' => 'Retrieve Transaksi Success',
                'data' => $transaksi
            ], 200);// Found
        }

        return response([
            'message' => 'Transaksi Not Found',
            'data' => null
        ], 200);// not Found
    }

    public function showAllByCustomer(Request $request, $idCustomer){
        $start = Carbon::now()->format('ymd');
        $transaksi = Transaksi::selectRaw("*, DATEDIFF(transaksis.tanggalWaktuSelesai, $start) as diff")
            ->where('transaksis.idCustomer', '=', $idCustomer)
            ->get();

        if(count($transaksi)>0){
            return response([
                'message' => 'Retrieve Transaksi by Customer Success',
                'data' => $transaksi
            ], 200);// Found
        }

        return response([
            'message' => 'Transaksi Not Found',
            'data' => null
        ], 200);// not Found
    }

    public function create(Request $request){
        $createTransaksi = $request->all();
        $validate = Validator::make($createTransaksi, [
            'idCustomer' => 'required',
            'idPembayaran' => 'required',
            'tanggalWaktuSewa' => 'required|date',
            'tanggalWaktuSelesai' => 'required|date',
        ]);// validai inputan

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);// if validate errors

        //$last_transaksi = DB::table('transaksis')->latest('idTransaksi')->first();
        $allTransaksi = Transaksi::all();
        if(count($allTransaksi) == 0){
            $new_id = '1';
        }else{
            $count = count($allTransaksi) + 1;
            //$substr_id = Str::substr((string)$last_transaksi->idTransaksi, 12);
            //$new_id = (int)$substr_id + 1;
        }
        $generateNumId = Str::padLeft((string)$count, 3, '0');

        $registerDate = Carbon::now()->format('ymd');
        $tanggalTransakasi = Carbon::now();

        if(!is_null($createTransaksi['idDriver'])){
            $jenisTransaksi = '01';
        }else{
            $jenisTransaksi = '00';
        }

        $transaksi = Transaksi::create([
            'idTransaksi' => 'TRN'.$registerDate.$jenisTransaksi.'-'.$generateNumId,
            'idPegawai' => $createTransaksi['idPegawai'],
            'idCustomer' => $createTransaksi['idCustomer'],
            'idPembayaran' => $createTransaksi['idPembayaran'],
            'idDriver' => $createTransaksi['idDriver'],
            'tanggalTransaksi' => $tanggalTransakasi,
            'tanggalWaktuSewa' => $createTransaksi['tanggalWaktuSewa'],
            'tanggalWaktuSelesai' => $createTransaksi['tanggalWaktuSelesai'],
            'tanggalWaktuKembali' => $createTransaksi['tanggalWaktuKembali'],
            'statusTransaksi' => 'Menunggu Konfirmasi',
        ]);
        return response([
            'message' => 'Add Transaksi Success',
            'data' => $transaksi
        ], 200); // return data berupa json
    }

    public function cekTanggalSewa(Request $request){
        $data = $request->all();
        $validate = Validator::make($data, [
            'tanggalWaktuSelesai' => 'after:tanggalWaktuSewa'
        ]);// validai inputan

        if($validate->fails())
            return response(['message' => array(array('Tanggal Sewa Invalid'))], 400);// if validate errors

        return response([
            'message' => 'Date Valid',
        ], 200); // return data berupa json
    }

    public function countCustomer($tglMulai, $tglSelesai){
        $order_count_customer = DB::table('transaksis')
                                ->join('users' , 'transaksis.idCustomer' , '=', 'users.idCustomer')
                                ->select('users.idCustomer', 'users.namaCustomer', DB::raw('count(*) as totalTransaksi'))
                                ->whereBetween('tanggalTransaksi', [$tglMulai, $tglSelesai])
                                ->where('transaksis.statusTransaksi', '=', 'Selesai')
                                ->groupBy('users.namaCustomer', 'users.idCustomer')
                                ->orderBy('totalTransaksi', 'desc')
                                ->take(5)
                                ->get();

        if(is_null($order_count_customer)){
            return response([
                'message' => 'Data Transaksi Tidak Ditemukan',
                'data' => null
            ], 400);
        }

        return response([
            'message' => 'Count Customer Transaksi Success',
            'data' => $order_count_customer,
        ], 200);
    }

    public function countDriver($tglMulai, $tglSelesai){
        $order_count_driver = DB::table('transaksis')
                                ->join('drivers' , 'transaksis.idDriver' , '=', 'drivers.idDriver')
                                ->select('drivers.idDriver', 'drivers.namaDriver', DB::raw('count(*) as totalTransaksi'))
                                ->whereBetween('tanggalTransaksi', [$tglMulai, $tglSelesai])
                                ->where('transaksis.statusTransaksi', '=', 'Selesai')
                                ->groupBy('drivers.namaDriver', 'drivers.idDriver')
                                ->orderBy('totalTransaksi', 'desc')
                                ->take(5)
                                ->get();

        if(is_null($order_count_driver)){
            return response([
                'message' => 'Data Transaksi Tidak Ditemukan',
                'data' => null
            ], 400);
        }

        return response([
            'message' => 'Count Driver Transaksi Success',
            'data' => $order_count_driver,
        ], 200);
    }

    public function countPendapatanMobil($tglMulai, $tglSelesai){
        $count_mobil = DB::table('transaksis')
                                ->join('pembayarans' , 'transaksis.idPembayaran' , '=', 'pembayarans.idPembayaran')
                                ->join('mobils' , 'pembayarans.idMobil' , '=', 'mobils.idMobil')
                                ->select('mobils.idMobil', 'mobils.tipeMobil', 'mobils.namaMobil', DB::raw('count(*) as jumlahPeminjaman'), DB::raw('SUM(pembayarans.totalBiayaMobil) as pendapatan'))
                                ->whereBetween('tanggalTransaksi', [$tglMulai, $tglSelesai])
                                ->where('transaksis.statusTransaksi', '=', 'Selesai')
                                ->groupBy('mobils.idMobil', 'mobils.tipeMobil', 'mobils.namaMobil')
                                ->orderBy('jumlahPeminjaman', 'desc')
                                ->get();

        if(count($count_mobil) > 0){
            return response([
                'message' => 'Count Pendapatan Mobil Success',
                'data' => $count_mobil
            ], 200);
        }

        return response([
            'message' => 'Data Transaksi Tidak Ditemukan',
            'data' => null,
        ], 400);
    }

    public function countDetilTransaksi($tglMulai, $tglSelesai){
        $count_detil = DB::table('transaksis')
                                ->join('users' , 'transaksis.idCustomer' , '=', 'users.idCustomer')
                                ->join('pembayarans' , 'transaksis.idPembayaran' , '=', 'pembayarans.idPembayaran')
                                ->join('mobils' , 'pembayarans.idMobil' , '=', 'mobils.idMobil')
                                ->select('users.namaCustomer', 'mobils.namaMobil', DB::raw('(CASE
                                    WHEN transaksis.idDriver is NULL THEN "Peminjaman Mobil"
                                    ELSE "Peminjaman Mobil + Driver"
                                    END) as jenisTransaksi'), DB::raw('count(*) as jumlahTransaksi'), DB::raw('SUM(pembayarans.totalBiaya) as pendapatan'))
                                ->whereBetween('tanggalTransaksi', [$tglMulai, $tglSelesai])
                                ->where('transaksis.statusTransaksi', '=', 'Selesai')
                                ->groupBy('users.namaCustomer', 'mobils.namaMobil', 'jenisTransaksi')
                                ->orderBy('users.namaCustomer', 'asc')
                                ->get();

        if(is_null($count_detil)){
            return response([
                'message' => 'Data Transaksi Tidak Ditemukan',
                'data' => null
            ], 400);
        }

        return response([
            'message' => 'Count Detil Transaksi Success',
            'data' => $count_detil,
        ], 200);
    }

    public function countPerformaDriver($tglMulai, $tglSelesai){
        $count_driver = DB::table('transaksis')
                                ->join('drivers' , 'transaksis.idDriver' , '=', 'drivers.idDriver')
                                ->select('drivers.idDriver', 'drivers.namaDriver', DB::raw('count(*) as totalTransaksi'),'drivers.rerataRating')
                                ->whereBetween('tanggalTransaksi', [$tglMulai, $tglSelesai])
                                ->where('transaksis.statusTransaksi', '=', 'Selesai')
                                ->groupBy('drivers.namaDriver', 'drivers.idDriver', 'drivers.rerataRating')
                                ->orderBy('totalTransaksi', 'desc')
                                ->get();

        if(is_null($count_driver)){
            return response([
                'message' => 'Data Transaksi Tidak Ditemukan',
                'data' => null
            ], 400);
        }

        return response([
            'message' => 'Count Driver Transaksi Success',
            'data' => $count_driver,
        ], 200);
    }

    public function update(Request $request, $idTransaksi){
        $transaksi = Transaksi::where('idTransaksi' , '=', $idTransaksi)->first(); // mencari data berdasarkan id

        if(is_null($transaksi)){
            return response([
                'message' => 'Transaksi not Found',
                'data' => null
            ], 400); // not Found
        }

        $updateTransaksi = $request->all();
        $validate = Validator($updateTransaksi, [
            'idCustomer' => 'required',
            'idPembayaran' => 'required',
            'tanggalWaktuSewa' => 'required|date',
            'tanggalWaktuSelesai' => 'required|date',
        ]);// validai inputan

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);// if validate errors

        $substr_TRN = Str::substr((string)$transaksi->idTransaksi, 0,3);
        $substr_Tanggal = Str::substr((string)$transaksi->idTransaksi, 3,6);
        $substr_id = Str::substr((string)$transaksi->idTransaksi, 12);
        if($updateTransaksi['idDriver'] == null || $updateTransaksi['idDriver'] == 'null' ){
            $jenisTransaksi = '00';
        }else{
            $jenisTransaksi = '01';
        }

        //menimpa data
        $transaksi->idTransaksi = $substr_TRN.$substr_Tanggal.$jenisTransaksi.'-'.$substr_id;
        $transaksi->idPegawai = $updateTransaksi['idPegawai'];
        $transaksi->idCustomer = $updateTransaksi['idCustomer'];
        $transaksi->idPembayaran = $updateTransaksi['idPembayaran'];
        $transaksi->idDriver = $updateTransaksi['idDriver'];
        $transaksi->tanggalWaktuSelesai = $updateTransaksi['tanggalWaktuSelesai'];
        $transaksi->tanggalWaktuSewa = $updateTransaksi['tanggalWaktuSewa'];
        $transaksi->tanggalWaktuKembali = $updateTransaksi['tanggalWaktuKembali'];

        if($transaksi->save()){
            return response([
                'message' => 'Update Transaksi Success',
                'data' => $transaksi
            ], 200);
        }

        return response([
            'message' => 'Update Transaksi Failed',
            'data' => null
        ], 400);
    }

    public function updateStatus(Request $request, $idTransaksi){
        $transaksi = Transaksi::where('idTransaksi' , '=', $idTransaksi)->first(); // mencari data berdasarkan id

        if(is_null($transaksi)){
            return response([
                'message' => 'Transaksi not Found',
                'data' => null
            ], 400); // not Found
        }

        $updateTransaksi = $request->all();
        $validate = Validator($updateTransaksi, [
            'statusTransaksi' => 'required|max:60',
        ]);// validai inputan

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);// if validate errors

        //menimpa data
        $transaksi->statusTransaksi = $updateTransaksi['statusTransaksi'];

        if($transaksi->save()){
            return response([
                'message' => 'Update Status Transaksi Success',
                'data' => $transaksi
            ], 200);
        }

        return response([
            'message' => 'Update Status Transaksi Failed',
            'data' => null
        ], 400);
    }

    public function updateStatusFirstTime(Request $request, $idTransaksi){
        $transaksi = Transaksi::where('idTransaksi' , '=', $idTransaksi)->first(); // mencari data berdasarkan id

        if(is_null($transaksi)){
            return response([
                'message' => 'Transaksi not Found',
                'data' => null
            ], 400); // not Found
        }

        $updateTransaksi = $request->all();
        $validate = Validator($updateTransaksi, [
            'statusTransaksi' => 'required|max:60',
            'idPegawai' => 'required'
        ]);// validai inputan

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);// if validate errors

        //menimpa data
        $transaksi->statusTransaksi = $updateTransaksi['statusTransaksi'];
        $transaksi->idPegawai = $updateTransaksi['idPegawai'];

        if($transaksi->save()){
            return response([
                'message' => 'Update Status Transaksi Success',
                'data' => $transaksi
            ], 200);
        }

        return response([
            'message' => 'Update Status Transaksi Failed',
            'data' => null
        ], 400);
    }

    public function updateRate(Request $request, $idTransaksi){
        $transaksi = Transaksi::where('idTransaksi' , '=', $idTransaksi)->first(); // mencari data berdasarkan id

        if(is_null($transaksi)){
            return response([
                'message' => 'Transaksi not Found',
                'data' => null
            ], 400); // not Found
        }

        $updateTransaksi = $request->all();
        $validate = Validator($updateTransaksi, [
            'rateDriver' => 'required|numeric',
            'performaDriver' => 'required|max:100',
        ]);// validai inputan

        if($updateTransaksi['performaDriver'] == null ){
            return response(['message' => "Performa Driver Kosong"], 400);// if validate errors
        }

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);// if validate errors

        $transaksi->rateDriver = $updateTransaksi['rateDriver'];
        $transaksi->performaDriver = $updateTransaksi['performaDriver'];

        if($transaksi->save()){
            return response([
                'message' => 'Update Rate Success',
                'data' => $transaksi
            ], 200);
        }

        return response([
            'message' => 'Update Rate Failed',
            'data' => null
        ], 400);
    }

}
