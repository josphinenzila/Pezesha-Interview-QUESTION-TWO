<?php

namespace App\Http\Controllers;

use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class FileUploadController extends Controller
{

    public function index()
    {
        return view('upload-file');
    }

    public function import(Request $request)
    {
        //validate the xls file
        $this->validate($request, array(
            'file' => 'required|max:100000',
        ));

        if ($request->hasFile('file')) {
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {

                $path = $request->file->getRealPath();
                /*   $data = Excel::load($path, function($reader) {
                })->get(); */
                $data = csvToArray($request->file('file'));
                if (!empty($data)) {

                    foreach ($data as $key => $value) {
                        $insert[] = [
                            'invoiceNo' => $value['InvoiceNo'],
                            'stockCode' => $value['StockCode'],
                            'description' => $value['Description'],
                            'quantity' => $value['Quantity'],
                            'invoiceDate' => $value['InvoiceDate'],
                            'unitPrice' => $value['UnitPrice'],
                            'customerId' => $value['CustomerID'],
                            'country' => $value['Country'],
                        ];

                    }

                    if (!empty($insert)) {

                        $insertData = DB::table('file_uploads')->insert($insert);
                        if ($insertData) {
                            Session::flash('success', 'Your Data has successfully imported');
                        } else {
                            Session::flash('error', 'Error inserting the data..');
                            return back();
                        }
                    }
                }

                return back();

            } else {
                Session::flash('error', 'File is a ' . $extension . ' file.!! Please upload a valid xls/csv file..!!');
                return back();
            }
        }
    }
}
