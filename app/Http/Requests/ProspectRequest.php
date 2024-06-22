<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ProspectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $prospect_type = Request::input('prospect_type_id');
        $transaction_type = Request::input('transaction_type_id');

        if (($prospect_type && in_array($prospect_type, [1,2])) && 
            ($transaction_type && in_array($transaction_type, [1,2,3]))) {
            if ($prospect_type == 1) {
                $prospect_rules = [
                    'year' => 'required|date_format:Y',
                    'ams_customer_id' => 'required|integer|exists:ams_customers,id',
                ];
            } else if ($prospect_type == 2) {
                $prospect_rules = [
                    'year' => 'required|date_format:Y',
                    'ams_customer_id' => 'required|integer|exists:ams_customers,id',
                    'strategic_initiative_id' => 'required|integer|exists:strategic_initiatives,id',
                    'pm_id' => 'required|integer|exists:users,id',
                ];
            }
    
            if (in_array($transaction_type, [1,2])) {
                $transaction_rules = [
                    'tmb' => 'required|array',
                    'tmb.*.product' => 'required|array',
                    'tmb.*.product.*.product_id' => 'required|integer|exists:products,id',
                    'tmb.*.product.*.aircraft_type' => 'sometimes|required',
                    'tmb.*.product.*.component' => 'sometimes|required',
                    'tmb.*.product.*.engine' => 'sometimes|required',
                    'tmb.*.product.*.igte' => 'sometimes|required',
                    'tmb.*.product.*.learning' => 'sometimes|required',
                    'tmb.*.product.*.market_share'  => 'required|numeric',
                    'tmb.*.product.*.remark' => 'required|string',
                    'tmb.*.product.*.maintenance_id' => 'required',
                ];
            } else if ($transaction_type == 3) {
                $transaction_rules = [
                    'pbth' => 'required|array',
                    'pbth.*.product_id' => 'required|integer|exists:products,id',
                    'pbth.*.aircraft_type_id' => 'required|integer|exists:ac_type_id,id',
                    'pbth.*.target' => 'required|array',
                    'pbth.*.target.*.month' => 'required|date_format:F',
                    'pbth.*.target.*.rate' => 'required|numeric',
                    'pbth.*.target.*.flight_hour' => 'required|numeric',
                ];
            }

            return array_merge($prospect_rules, $transaction_rules);
        } else {
            return [
                'prospect_type_id' => 'required|between:1,2',
                'transaction_type_id' => 'required|between:1,3',
            ];
        }
    }

    public function messages()
    {
        return [
            'prospect_type_id.required' => 'The Prospect Type is required.',
            'prospect_type_id.between' => 'The Prospect Type must be between Organic and In-organic.',
            'transaction_type_id.required' => 'The Transaction Type is required.',
            'transaction_type_id.between' => 'The Transaction must be between TMB Retail, TMB Project and PBTH.',
            'year.required' => 'The Year field is required.',
            'year.date_format' => 'The Year format is invalid.',
            'ams_cuseromer_id.required' => 'The AMS & Area field is required.',
            'ams_cuseromer_id.exists' => 'The selected AMS & Area is invalid.',
            'strategic_initiative_id.required' => 'The Strategic Inititative field is required.',
            'strategic_initiative_id.exists' => 'The selected Strategic Inititative is invalid.',
            'pm_id.required' => 'The Project Manager field is required.',
            'pm_id.exists' => 'The selected Project Manager is invalid.',
            'tmb.required' => 'The TMB data collection is required.',
            'tmb.array' => 'The TMB data colection must be an array.',
            'tmb.*.product.required' => 'The Product data collection is required.',
            'tmb.*.product.array' => 'The Product data collection must be an array.',
            'tmb.*.product.*.product_id.required' => 'The Product field is required.',
            'tmb.*.product.*.product_id.exists' => 'The selected Product is invalid.',
            'tmb.*.product.*.aircraft_type.required' => 'The Aircraft Type field is required.',
            'tmb.*.product.*.component.required' => 'The Component field is required.',
            'tmb.*.product.*.igte.required' => 'The IGTE field is required.',
            'tmb.*.product.*.learning.required' => 'The Learning field is required.',
            'tmb.*.product.*.apu.required' => 'The APU field is required.',
            'tmb.*.product.*.engine.required' => 'The Engine field is required.',
            'tmb.*.product.*.market_share.required' => 'The Market Share field is required.',
            'tmb.*.product.*.market_share.numeric' => 'The Market Share field must be a number.',
            'tmb.*.product.*.remark.required' => 'The Remarks field is required.',
            'tmb.*.product.*.remark.string' => 'The Remarks field must be a string.',
            'tmb.*.product.*.maintenance_id.required' => 'The Maintenance field is required.',
            'pbth.required' => 'The PBTH data collection is required.',
            'pbth.array' => 'The PBTH data colection must be an array.',
            'pbth.*.product_id.required' => 'The Product field is required.',
            'pbth.*.product_id.exists' => 'The selected Product is invalid.',
            'pbth.*.aircraft_type_id.required' => 'The Aircraft Type field is required.',
            'pbth.*.aircraft_type_id.exists' => 'The selected Aircraft Type is invalid.',
            'pbth.*.target.required' => 'The Target data collection is required.',
            'pbth.*.target.array' => 'The Target data collection must be an array.',
            'pbth.*.target.*.month.required' => 'The Month field is required.',
            'pbth.*.target.*.month.date_format' => 'The Month format is invalid.',
            'pbth.*.target.*.rate.required' => 'The Rate field is required.',
            'pbth.*.target.*.rate.numeric' => 'The Rate field must be a number.',
            'pbth.*.target.*.flight_hour.required' => 'The Flight Hour field is required.',
            'pbth.*.target.*.flight_hour.numeric' => 'The Flight Houre field must be a number.',
        ];
    }
}
