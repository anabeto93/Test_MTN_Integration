<?php

namespace App\Http\Controllers;

use App\Repositories\Mtn\MtnRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MtnController extends Controller 
{
	private $mtn;

	public function __construct(MtnRepository $mtn)
    {
        error_reporting(E_ALL);
        $this->mtn = $mtn;
    }

    public function transact(Request $request)
    {
        $this->validate($request, [
            'merchant_id'       => 'bail|required|size:12|alpha_dash',
            'processing_code'   => 'bail|required|digits:6|in:000000,000100,400000,400010,400020,400100,400110,400120,400200,400210,400220,404000,404010,404020,000200',
            'transaction_id'    => 'bail|required|digits:12',
            'desc'              => 'bail|required|min:10|max:100',
            'amount'            => 'bail|required|digits:12',

            'pass_code'         => 'bail|size:32|required_if:r-switch,FLT',
            'r-switch'          => 'bail|required|in:TGO,MTN,ATL,MAS,VIS,VDF,FLT|size:3',
            'voucher_code'      => 'bail|required_if:r-switch,VDF',
            'subscriber_number' => 'bail|required_if:processing_code,000200|required_if:processing_code,400200|min:10|max:12',
            '3d_url_response'   => 'bail|required_if:processing_code,000000|required_if:processing_code,000100|required_if:processing_code,400000|required_if:processing_code,400100|required_if:processing_code,400110|required_if:processing_code,400120|url',
            'cvv'               => 'bail|required_if:processing_code,000000|required_if:processing_code,000100|required_if:processing_code,400000|required_if:processing_code,400100|required_if:processing_code,400110|required_if:processing_code,400120|min:3|max:4',
            'pan'               => 'bail|required_if:processing_code,000000|required_if:processing_code,000100|required_if:processing_code,400000|required_if:processing_code,400100|required_if:processing_code,400110|required_if:processing_code,400120|digits_between:16,20',
            'exp_month'         => 'bail|required_if:processing_code,000000|required_if:processing_code,000100|required_if:processing_code,400000|required_if:processing_code,400100|required_if:processing_code,400110|required_if:processing_code,400120|digits:2',
            'exp_year'          => 'bail|required_if:processing_code,000000|required_if:processing_code,000100|required_if:processing_code,400000|required_if:processing_code,400100|required_if:processing_code,400110|required_if:processing_code,400120|digits:2',
            'account_issuer'    => 'bail|required_if:processing_code,400000|required_if:processing_code,400100|required_if:processing_code,400200|required_if:processing_code,400110|required_if:processing_code,400120|required_if:processing_code,400010|required_if:processing_code,400020',
            'account_number'    => 'bail|required_if:processing_code,400000|required_if:processing_code,400100|required_if:processing_code,400200|required_if:processing_code,400110|required_if:processing_code,400120|required_if:processing_code,400010|required_if:processing_code,400020'
        ], [
            'processing_code.in'=> 'The selected transaction type is invalid. Please refer to the documentation.',
            'amount.digits'      => 'Format error: Amount must be 12 digits. Eg 000000000100 for GHS 1.00',
            'merchant_id.size'  => 'Merchant id must be 12 characters long'
        ]);

        # Start Processing The Transaction

        $transaction = [];

        $transaction['fld_002']         =   $request->input('subscriber_number', null);
        $transaction['voucher_code']    =   $request->input('voucher_code', null);
        $transaction['fld_003'] = $request->input('processing_code');
        $transaction['fld_004'] = $request->input('amount');
        $transaction['fld_009'] = $request->input('device_type', 'N');
        $transaction['fld_011'] = substr(explode(' ', microtime())[1], 0, 4).str_shuffle(explode('.', explode(' ', microtime())[0])[1]);
        $transaction['fld_014'] = null;
        $transaction['fld_037'] = $request->input('transaction_id');

        $transaction['fld_042'] = $request->input('merchant_id');
        $transaction['fld_057'] = $request->input('r-switch');
        $transaction['fld_116'] = $request->input('desc');

        $transaction['fld_103'] = $request->input('account_number', null);
        $transaction['fld_117'] = $request->input('account_issuer', null);
        $transaction['fld_123'] = null;

        # Set Reserved For Future Use Fields
        $transaction['rfu_001'] = $request->input('rfu_001', 'null');
        $transaction['rfu_002'] = $request->input('rfu_002', 'null');
        $transaction['rfu_003'] = $request->input('rfu_003', 'null');
        $transaction['rfu_004'] = $request->input('rfu_004', 'null');
        $transaction['rfu_005'] = $request->input('rfu_005', 'null');

        Log::info('The transaction array');
        Log::debug($transaction);

        $response = $this->mtn->transact($transaction);

        return response()->json($transaction);
    }
}