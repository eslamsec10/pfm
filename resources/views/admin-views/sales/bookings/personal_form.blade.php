<div class="row">
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('name' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="name" value="{{ (isset($booking->tenant)) ? $booking->name : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="name" class="title-color">{{  ui_change('Gender' , 'property_transaction')   }}
            </label>
            <select class="js-select2-custom form-control" name="gender" >
                <option selected disabled>{{  ui_change('select' , 'property_transaction')  }} </option>
                <option value="male" {{ (isset($booking->tenant) ? (($booking->gender == 'male' ) ? 'selected' : '') : '') }}>{{  ui_change('male' , 'property_transaction')  }} </option>
                <option value="female" {{ (isset($booking->tenant) ? (($booking->gender == 'female' ) ? 'selected' : '') : '') }}>{{  ui_change('female' , 'property_transaction')  }} </option>

            </select>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('CPR/ID_No.' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="id_number" value="{{ (isset($booking->tenant)) ? $booking->id_number : '' }}">
            <input type="hidden" class="form-control" name="type" value="individual">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Nick_Name' , 'property_transaction')   }}</label>
            <input type="text" class="form-control" name="nick_name" value="{{ (isset($booking->tenant)) ? $booking->nick_name : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Contact_Person' , 'property_transaction')   }}</label>
            <input type="text" class="form-control" name="contact_person" value="{{ (isset($booking->tenant)) ? $booking->contact_person : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Designation' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="designation" value="{{ (isset($booking->tenant)) ? $booking->designation : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Contact_No.' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="contact_no" value="{{ (isset($booking->tenant)) ? $booking->contact_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Whatsapp_No.' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="whatsapp_no" value="{{ (isset($booking->tenant)) ? $booking->whatsapp_no : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Company_Name' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="company_name" value="{{ (isset($booking->tenant)) ? $booking->company_name : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Fax_No' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="fax_no" value="{{ (isset($booking->tenant)) ? $booking->fax_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Telephone_No' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="telephone_no" value="{{ (isset($booking->tenant)) ? $booking->telephone_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Other_Contact_No.' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="other_contact_no" value="{{ (isset($booking->tenant)) ? $booking->other_contact_no : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Address_Line_1' , 'property_transaction')   }}</label>
            <input type="text" class="form-control" name="address1" value="{{ (isset($booking->tenant)) ? $booking->address1 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Address_Line_2' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="address2" value="{{ (isset($booking->tenant)) ? $booking->address2 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Address_Line_3' , 'property_transaction')   }}</label>
            <input type="text" class="form-control" name="address3" value="{{ (isset($booking->tenant)) ? $booking->address3 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('City' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="city" value="{{ (isset($booking->tenant)) ? $booking->city : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('State' , 'property_transaction')   }}</label>
            <input type="text" class="form-control" name="state" value="{{ (isset($booking->tenant)) ? $booking->state : '' }}">
        </div>
    </div>



    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="name" class="title-color">{{  ui_change('country' , 'property_transaction')   }}
            </label>
            <select class="js-select2-custom form-control" name="country_id" >
                <option selected disabled>{{  ui_change('select' , 'property_transaction')  }} </option>
                @foreach ($country_master as $country_master_item)
                    <option value="{{ $country_master_item->id }}" {{ (isset($booking->tenant) ? (($booking->country_id == $country_master_item->id ) ? 'selected' : '') : '') }}>
                        {{ $country_master_item->country->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="name" class="title-color">{{  ui_change('nationality_of_owner' , 'property_transaction')   }}
            </label>
            <select class="js-select2-custom form-control" name="nationality_id" >
                <option selected disabled>{{  ui_change('select' , 'property_transaction')  }} </option>
                @foreach ($country_master as $country_master_item)
                    <option value="{{ $country_master_item->id }}" {{ (isset($booking->tenant) ? (($booking->nationality_id == $country_master_item->id ) ? 'selected' : '') : '') }}>
                        {{ $country_master_item->country->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Passport_No.' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="passport_no" value="{{ (isset($booking->tenant)) ? $booking->passport_no : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Email_Address' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="email1" value="{{ (isset($booking->tenant)) ? $booking->email1 : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{  ui_change('Secondary_Email' , 'property_transaction')  }}</label>
            <input type="text" class="form-control" name="email2" value="{{ (isset($booking->tenant)) ? $booking->email2 : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="name" class="title-color">{{  ui_change('Living_Status' , 'property_transaction')   }}
            </label>
            <select class="js-select2-custom form-control" name="live_with_id" >

                @foreach ($live_withs as $live_with_item)
                    <option value="{{ $live_with_item->id }}" {{ (isset($booking->tenant) ? (($booking->live_with_id == $live_with_item->id ) ? 'selected' : '') : '') }}>
                        {{ $live_with_item->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>


</div>

