<div class="row">
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Company_Name' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="company_name" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->company_name : '' }}">
        </div>
    </div>

<div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="name" class="title-color">{{  ui_change('business_activity' , 'property_transaction')  }}
            </label>
            <select class="js-select2-custom form-control" name="business_activity_id" >

                @foreach ($business_activities as $business_activity_item)
                    <option value="{{ $business_activity_item->id }}" {{ (isset($proposal->tenant)) ? (($business_activity_item->id == $proposal->business_activity_id ) ? 'selected' : '') : '' }}>
                        {{ $business_activity_item->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('CR/Registration_No.' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="registration_no" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->registration_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Group_Company_Name' , 'property_transaction')   }}</label>
            <input type="text" class="form-control" name="group_company_name" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->group_company_name : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Contact_Person' , 'property_transaction')   }}</label>
            <input type="text" class="form-control" name="contact_person" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->contact_person : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Designation' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="designation" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->designation : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Contact_No.' , 'property_transaction')   }}</label>
            <input type="text" class="form-control" name="contact_no" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->contact_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Whatsapp_No.' , 'property_transaction')   }}</label>
            <input type="text" class="form-control" name="whatsapp_no" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->whatsapp_no : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Fax_No' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="fax_no" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->fax_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Telephone_No' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="telephone_no" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->telephone_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Other_Contact_No.' , 'property_transaction')   }}</label>
            <input type="text" class="form-control" name="other_contact_no" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->other_contact_no : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Address_Line_1' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="address1" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->address1 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Address_Line_2' , 'property_transaction')   }}</label>
            <input type="text" class="form-control" name="address2" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->address2 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Address_Line_3' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="address3" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->address3 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('City' , 'property_transaction')   }}</label>
            <input type="text" class="form-control" name="city" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->city : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('State' , 'property_transaction')   }}</label>
            <input type="text" class="form-control" name="state" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->state : '' }}">
        </div>
    </div>



    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="name" class="title-color">{{  ui_change('country' , 'property_transaction')   }}
            </label>
            <select class="js-select2-custom form-control" name="country_id" >
                <option selected disabled>{{  ui_change('select' , 'property_transaction')   }} </option>
                @foreach ($country_master as $country_master_item)
                    <option value="{{ $country_master_item->id }}" {{ (isset($proposal->tenant)) ? (($country_master_item->id == $proposal->tenant->country_id) ? 'selected' : '') : '' }}>
                        {{ $country_master_item->country->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Email_Address' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="email1" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->email1 : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Secondary_Email' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="email2" value="{{ (isset($proposal->tenant)) ? $proposal->tenant->email2 : '' }}">
        </div>
    </div>



</div>

