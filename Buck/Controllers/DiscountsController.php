<?php

namespace Statamic\Addons\Buck\Controllers;

use Statamic\API\File;
use Statamic\API\Path;
use Statamic\API\YAML;
use Statamic\API\Fieldset;
use Illuminate\Http\Request;
use Statamic\Extend\Controller;
use Statamic\CP\Publish\ProcessesFields;
use Illuminate\Support\Facades\Validator;
use Statamic\Addons\Buck\Models\Discount;
use Statamic\CP\Publish\ValidationBuilder;

class DiscountsController extends Controller
{
    use ProcessesFields;

    public function __construct()
    {
        if (!Fieldset::exists('discount')) {
            $path = Path::assemble(
                $this->getDirectory(),
                'resources',
                'fieldsets',
                'discount.yaml'
            );

            $fieldset = Fieldset::create('discount', YAML::parse(File::get($path)));

            $fieldset->save();
        }
    }

    /**
     * Listing of all discounts
     */
    public function index()
    {
        return $this->view('discounts.index', [
            'title' => 'Discounts',
            'discounts' => Discount::all(),
        ]);
    }

    /**
     * The form to create a new discount
     */
    public function create()
    {
        return $this->view('discounts.create', [
            'title' => 'New Discount',
            'data' => $this->prepareData([]),
        ]);
    }

    /**
     * Endpoint for storing a new discount
     */
    public function store(Request $request)
    {
        $validator = $this->validator();

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->toArray()];
        }

        $data = $this->processFields($this->fieldset(), $request->fields);

        $discount = Discount::create($data);

        return $this->successResponse($discount->id);
    }

    /**
     * The form to edit an existing discount
     */
    public function edit($id)
    {
        $status = Discount::find($id);

        return $this->view('discounts.edit', [
            'title' => $discount->id,
            'discount' => $discount,
            'data' => $this->prepareData($discount->toArray()),
        ]);
    }

    /**
     * Endpoint for updating an existing discount
     */
    public function update(Request $request, $id)
    {
        $discount = Discount::find($id);
        $validator = $this->validator();

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->toArray()];
        }

        $data = $this->processFields($this->fieldset(), $request->fields);

        // Statamic's fieldtype processing would result in any 'null' values being stripped out from $data so they aren't saved in files.
        // If $data is missing a key, it would simply be ignored when using Eloquent's update() method, leaving any existing data in
        // the database. Because of this, we'll merge in any nulls so that the appropriate values get removed from the DB row.
        $data = $this->mergeNulls($data);

        $discount->update($data);

        return $this->successResponse($id);
    }

    /**
     * Get a Validator instance with rules and attributes based on the fieldset
     */
    private function validator()
    {
        $validationBuilder = new ValidationBuilder(request()->fields, $this->fieldset());

        $validationBuilder->build();

        return Validator::make(
            request()->all(),
            $validationBuilder->rules(),
            [],
            $validationBuilder->attributes()
        );
    }

    /**
     * Prepare data to be used in the publish form.
     * It will add nulls (or appropriate default values) for any fields defined in the fieldset
     * that haven't been provided in the array. Vue needs the values to exist for reactivity.
     */
    private function prepareData($data)
    {
        return $this->preProcessWithBlankFields($this->fieldset(), $data);
    }

    /**
     * Get the fieldset.
     */
    private function fieldset()
    {
        return Fieldset::get('discount');
    }

    /**
     * Merge in null for every field in the fieldset that was not in the given array.
     */
    private function mergeNulls($data)
    {
        return collect($this->fieldset()->inlinedFields())
            ->keys()
            ->mapWithKeys(function ($field) {
                return [$field => null];
            })->merge($data)->all();
    }

    /**
     * The response to be returned for a successful save.
     */
    private function successResponse($id)
    {
        $message = 'Discount saved';

        // Actions that trigger an actual redirect should add the message into the session's flash data.
        if (!request()->continue || request()->new) {
            $this->success($message);
        }

        return [
            'success' => true,
            'redirect' => request()->continue
                ? route('discounts.edit', ['discounts' => $id])
                : route('discounts.index'),
            'message' => $message,
        ];
    }
}
