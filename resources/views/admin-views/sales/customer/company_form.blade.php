<div class="row">
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('company_name', 'property_master') }} <span
                    class="text-danger"> *</span></label>
            <input type="text" class="form-control" name="company_name"
                value="{{ isset($customer) ? $customer->company_name : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="name" class="title-color">{{ ui_change('business_activity', 'property_master') }} <span
                    class="text-danger"> *</span>
            </label>
            <select class="js-select2-custom form-control" name="business_activity_id" required>
                <option selected>{{ ui_change('select', 'property_master') }}
                </option>
                @foreach ($business_activities as $business_activity_item)
                    <option value="{{ $business_activity_item->id }}"
                        {{ isset($customer) ? ($customer->business_activity_id == $business_activity_item->id ? 'selected' : '') : '' }}>
                        {{ $business_activity_item->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="name" class="title-color">{{ ui_change('country', 'property_master') }}<span
                    class="text-danger"> *</span>
            </label>
            <select class="js-select2-custom form-control" name="country_id" required>
                <option selected disabled>{{ ui_change('select', 'property_master') }} </option>
                @foreach ($country_master as $country_master_item)
                    <option value="{{ $country_master_item->id }}"
                        {{ isset($customer) ? ($customer->country_id == $country_master_item->id ? 'selected' : '') : '' }}>
                        {{ $country_master_item->country->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('contact_person', 'property_master') }}<span
                    class="text-danger"> *</span></label>
            <input type="text" class="form-control" name="contact_person"
                value="{{ isset($customer) ? $customer->contact_person : '' }}">
        </div>
    </div>
    <input type="hidden" class="form-control" name="type" value="company">

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token"
                class="title-color">{{ ui_change('CR/_registration_no.', 'property_master') }}</label>
            <input type="text" class="form-control" name="registration_no"
                value="{{ isset($customer) ? $customer->registration_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('group_company_name', 'property_master') }}</label>
            <input type="text" class="form-control" name="group_company_name"
                value="{{ isset($customer) ? $customer->group_company_name : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('designation', 'property_master') }}</label>
            <input type="text" class="form-control" name="designation"
                value="{{ isset($customer) ? $customer->designation : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('contact_no.', 'property_master') }}</label>
            <input type="text" class="form-control" name="contact_no"
                value="{{ isset($customer) ? $customer->contact_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="whatsapp_dail_code" class="title-color">{{ ui_change('dail_code', 'property_master') }}</label>
            <select class="js-select2-custom form-control" name="whatsapp_dail_code">
                <option selected value="">{{ ui_change('select') }}</option>
                @foreach ($dail_code_main as $item_dail_code)
                    <option value="{{ $item_dail_code->dial_code }}"
                        {{ isset($customer) ? (old('whatsapp_dail_code') == $item_dail_code->dial_code ? 'selected' : ($item_dail_code->dial_code == $customer->whatsapp_dail_code ? 'selected' : '')) : '' }}>
                        {{ '+' . $item_dail_code->dial_code }}
                    </option>
                @endforeach
            </select>

        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('whatsapp_no.', 'property_master') }}</label>
            <input type="text" class="form-control" name="whatsapp_no"
                value="{{ isset($customer) ? $customer->whatsapp_no : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('fax_no', 'property_master') }}</label>
            <input type="text" class="form-control" name="fax_no"
                value="{{ isset($customer) ? $customer->fax_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('telephone_no', 'property_master') }}</label>
            <input type="text" class="form-control" name="telephone_no"
                value="{{ isset($customer) ? $customer->telephone_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token"
                class="title-color">{{ ui_change('other_contact_no.', 'property_master') }}</label>
            <input type="text" class="form-control" name="other_contact_no"
                value="{{ isset($customer) ? $customer->other_contact_no : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('address_line_1', 'property_master') }}</label>
            <input type="text" class="form-control" name="address1"
                value="{{ isset($customer) ? $customer->address1 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('address_line_2', 'property_master') }}</label>
            <input type="text" class="form-control" name="address2"
                value="{{ isset($customer) ? $customer->address2 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('address_line_3', 'property_master') }}</label>
            <input type="text" class="form-control" name="address3"
                value="{{ isset($customer) ? $customer->address3 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('city', 'property_master') }}</label>
            <input type="text" class="form-control" name="city"
                value="{{ isset($customer) ? $customer->city : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('state', 'property_master') }}</label>
            <input type="text" class="form-control" name="state"
                value="{{ isset($customer) ? $customer->state : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('email_address', 'property_master') }}</label>
            <input type="text" class="form-control" name="email1"
                value="{{ isset($customer) ? $customer->email1 : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('secondary_email', 'property_master') }}</label>
            <input type="text" class="form-control" name="email2"
                value="{{ isset($customer) ? $customer->email2 : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('upload_document', 'property_master') }}</label>
            <input type="file" class="form-control" name="document">
        </div>
    </div>

</div>
