<div class="row">
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('name' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="name" value="{{ (isset($proposal->customer)) ? $proposal->customer?->name : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="name" class="title-color">{{ ui_change('Gender'  , 'property_transaction')}}
            </label>
            <select class="js-select2-custom form-control" name="gender" >
                <option selected disabled>{{ ui_change('select' , 'property_transaction') }} </option>
                <option value="male" {{ (isset($proposal->customer) ? (($proposal->customer?->gender == 'male' ) ? 'selected' : '') : '') }}>{{ ui_change('male' , 'property_transaction') }} </option>
                <option value="female" {{ (isset($proposal->customer) ? (($proposal->customer?->gender == 'female' ) ? 'selected' : '') : '') }}>{{ ui_change('female' , 'property_transaction') }} </option> 
            </select>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('CPR_/_ID_No.' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="id_number" value="{{ (isset($proposal->customer)) ? $proposal->customer?->id_number : '' }}">
            <input type="hidden" class="form-control" name="type" value="individual">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Nick_Name' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="nick_name" value="{{ (isset($proposal->customer)) ? $proposal->customer?->nick_name : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Contact_Person' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="contact_person" value="{{ (isset($proposal->customer)) ? $proposal->customer?->contact_person : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Designation'  , 'property_transaction')}}</label>
            <input type="text" class="form-control" name="designation" value="{{ (isset($proposal->customer)) ? $proposal->customer?->designation : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Contact_No.' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="contact_no" value="{{ (isset($proposal->customer)) ? $proposal->customer?->contact_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Whatsapp_No.' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="whatsapp_no" value="{{ (isset($proposal->customer)) ? $proposal->customer?->whatsapp_no : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Company_Name' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="company_name" value="{{ (isset($proposal->customer)) ? $proposal->customer?->company_name : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Fax_No' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="fax_no" value="{{ (isset($proposal->customer)) ? $proposal->customer?->fax_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Telephone_No' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="telephone_no" value="{{ (isset($proposal->customer)) ? $proposal->customer?->telephone_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Other_Contact_No.' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="other_contact_no" value="{{ (isset($proposal->customer)) ? $proposal->customer?->other_contact_no : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Address_Line_1', 'property_transaction') }}</label>
            <input type="text" class="form-control" name="address1" value="{{ (isset($proposal->customer)) ? $proposal->customer?->address1 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Address_Line_2' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="address2" value="{{ (isset($proposal->customer)) ? $proposal->customer?->address2 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Address_Line_3', 'property_transaction') }}</label>
            <input type="text" class="form-control" name="address3" value="{{ (isset($proposal->customer)) ? $proposal->customer?->address3 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('City'  , 'property_transaction')}}</label>
            <input type="text" class="form-control" name="city" value="{{ (isset($proposal->customer)) ? $proposal->customer?->city : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('State' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="state" value="{{ (isset($proposal->customer)) ? $proposal->customer?->state : '' }}">
        </div>
    </div>

 

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Passport_No.' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="passport_no" value="{{ (isset($proposal->customer)) ? $proposal->customer?->passport_no : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Email_Address' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="email1" value="{{ (isset($proposal->customer)) ? $proposal->customer?->email1 : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Secondary_Email' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="email2" value="{{ (isset($proposal->customer)) ? $proposal->customer?->email2 : '' }}">
        </div>
    </div>
 

</div>

