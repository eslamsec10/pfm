<div class="row">
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('roles.name') }}</label>
            <input type="text" class="form-control" name="name">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="name" class="title-color">{{ __('Gender') }}
            </label>
            <select class="js-select2-custom form-control" name="gender" >
                <option selected disabled>{{ __('general.select') }} </option>
                <option value="male">{{ __('general.male') }} </option>
                <option value="female">{{ __('general.female') }} </option>

            </select>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('CPR/ID No.') }}</label>
            <input type="text" class="form-control" name="id_number">
            <input type="hidden" class="form-control" name="type" value="individual">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ __('Nick Name') }}</label>
            <input type="text" class="form-control" name="nick_name">
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
            <label for="token" class="title-color">{{ __('Company Name') }}</label>
            <input type="text" class="form-control" name="company_name">
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
            <label for="name" class="title-color">{{ __('country.nationality_of_owner') }}
            </label>
            <select class="js-select2-custom form-control" name="nationality_id" >
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
            <label for="token" class="title-color">{{ __('Passport No.') }}</label>
            <input type="text" class="form-control" name="passport_no">
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

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="name" class="title-color">{{ __('Living Status *') }}
            </label>
            <select class="js-select2-custom form-control" name="live_with_id" >
                <option selected>{{ __('general.select') }}
                </option>
                @foreach ($live_withs as $live_with_item)
                    <option value="{{ $live_with_item->id }}">
                        {{ $live_with_item->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>


</div>

