<div class="row">
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('Company Name') }}</label>
            <input type="text" class="form-control" name="company_name">
        </div>
    </div>

<div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="name" class="title-color">{{ __('property_master.business_activity') }}
            </label>
            <select class="js-select2-custom form-control" name="business_activity_id" >
                <option selected>{{ __('general.select') }}
                </option>
                @foreach ($business_activities as $business_activity_item)
                    <option value="{{ $business_activity_item->id }}">
                        {{ $business_activity_item->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('CR/Registration No.') }}</label>
            <input type="text" class="form-control" name="registration_no">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('Group Company Name') }}</label>
            <input type="text" class="form-control" name="group_company_name">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('Contact Person') }}</label>
            <input type="text" class="form-control" name="contact_person">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('Designation') }}</label>
            <input type="text" class="form-control" name="designation">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('Contact No.') }}</label>
            <input type="text" class="form-control" name="contact_no">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('Whatsapp No.') }}</label>
            <input type="text" class="form-control" name="whatsapp_no">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('Fax No') }}</label>
            <input type="text" class="form-control" name="fax_no">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('Telephone No') }}</label>
            <input type="text" class="form-control" name="telephone_no">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('Other Contact No.') }}</label>
            <input type="text" class="form-control" name="other_contact_no">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('Address Line 1') }}</label>
            <input type="text" class="form-control" name="address1">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('Address Line 2') }}</label>
            <input type="text" class="form-control" name="address2">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('Address Line 3') }}</label>
            <input type="text" class="form-control" name="address3">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('City') }}</label>
            <input type="text" class="form-control" name="city">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('State') }}</label>
            <input type="text" class="form-control" name="state">
        </div>
    </div>



    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="name" class="title-color">{{ __('country.country') }}
            </label>
            <select class="js-select2-custom form-control" name="country_id" >
                <option selected disabled>{{ __('general.select') }} </option>
                @foreach ($country_master as $country_master_item)
                    <option value="{{ $country_master_item->id }}">
                        {{ $country_master_item->country->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('Email Address') }}</label>
            <input type="text" class="form-control" name="email1">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('Secondary Email') }}</label>
            <input type="text" class="form-control" name="email2">
        </div>
    </div>



</div>

