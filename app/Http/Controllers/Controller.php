<?php

namespace Bhoechie\Checklist\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public $payload;

    /**
     * Validate the given request with the given rules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     *
     * @throws ValidationException
     */
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        // dynamicly get the request payload, is json or form-data
        $payload = [];
        $newRules = [];
        $newMessages = [];
        $isJson = false;
        if (empty($request->json()->all())) {
            $payload = $request->all();
            $newRules = $rules;
            $newMessages = $messages;
        } else {
            $isJson = true;
            //get json payload
            $payload = $request->json()->all();
            //make sure json format is data -> attributes
            if (!isset($rules['data'])) {
                $newRules['data'] = 'required|array';
                $newRules['data.attributes'] = 'required|array';
                $newMessages['data.required'] = 'json payload must wrap data with data.attributes';
                $newMessages['data.attributes.required'] = 'json payload must wrap data with data.attributes';
                //manipulate existing rules
                foreach ($rules as $key => $value) {
                    $newRules["data.attributes.{$key}"] = $value;
                }
                foreach ($messages as $key => $value) {
                    $newMessages["data.attributes.{$key}"] = $value;
                }
            }
        }

        $validator = $this->getValidationFactory()->make($payload, $newRules, $newMessages, $customAttributes);

        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        if ($isJson) {
            $this->payload = date_get($payload, 'data.attributes');
        } else {

            $this->payload = $payload;
        }

        return $this->extractInputFromRules($request, $rules);
    }

    /**
     * Get the input payload for the request.
     *
     * @param  string|null  $key
     * @param  mixed   $default
     * @return \mixed
     */
    public function input($key = null, $default = null)
    {

        if (is_null($this->payload)) {
            $this->payload = [];
        }

        return data_get($this->payload, $key, $default);
    }
}
