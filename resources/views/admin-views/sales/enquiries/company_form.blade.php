<div class="row">
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Company_Name' , 'property_transaction') }}  <span class="text-danger"> *</span></label></label>
            <input type="text" class="form-control" name="company_name" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->company_name : '' }}">
        </div>
    </div>

<div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="name" class="title-color">{{ ui_change('business_activity' , 'property_transaction') }} <span class="text-danger"> *</span></label>
            </label>
            <select class="js-select2-custom form-control" name="business_activity_id" >

                @foreach ($business_activities as $business_activity_item)
                    <option value="{{ $business_activity_item->id }}" {{ (isset($enquiry->tenant)) ? (($business_activity_item->id == $enquiry->business_activity_id ) ? 'selected' : '') : '' }}>
                        {{ $business_activity_item->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="name" class="title-color">{{ ui_change('country' , 'property_transaction') }} <span class="text-danger"> *</span></label>
            </label>
            <select class="js-select2-custom form-control" name="country_id" >
                <option selected disabled>{{ ui_change('select' , 'property_transaction') }} </option>
                @foreach ($country_master as $country_master_item)
                    <option value="{{ $country_master_item->id }}" {{ (isset($enquiry->tenant)) ? (($country_master_item->id == $enquiry->tenant->country_id) ? 'selected' : '') : '' }}>
                        {{ $country_master_item->country->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('CR_/_Registration_No.'  , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="registration_no" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->registration_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Group_Company_Name' , 'property_transaction') }}  <span class="text-danger"> *</span></label></label>
            <input type="text" class="form-control" name="group_company_name" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->group_company_name : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Contact_Person' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="contact_person" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->contact_person : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Designation'  , 'property_transaction')}}</label>
            <input type="text" class="form-control" name="designation" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->designation : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Contact_No.' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="contact_no" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->contact_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Whatsapp_No.' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="whatsapp_no" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->whatsapp_no : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Fax_No' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="fax_no" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->fax_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Telephone_No' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="telephone_no" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->telephone_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Other_Contact_No.' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="other_contact_no" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->other_contact_no : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Address_Line_1' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="address1" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->address1 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Address_Line_2' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="address2" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->address2 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Address_Line_3' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="address3" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->address3 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('City'  , 'property_transaction')}}</label>
            <input type="text" class="form-control" name="city" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->city : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('State' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="state" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->state : '' }}">
        </div>
    </div> 
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Email_Address' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="email1" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->email1 : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Secondary_Email' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="email2" value="{{ (isset($enquiry->tenant)) ? $enquiry->tenant->email2 : '' }}">
        </div>
    </div>



</div>

