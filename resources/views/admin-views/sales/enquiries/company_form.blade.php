<div class="row">
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Company_Name' , 'property_transaction') }}  <span class="text-danger"> *</span></label></label>
            <input type="text" class="form-control" name="company_name" value="{{ (isset($enquiry->customer)) ? $enquiry->customer->company_name : '' }}">
        </div>
    </div>

 
  
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('CR_/_Registration_No.'  , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="registration_no" value="{{ (isset($enquiry->customer)) ? $enquiry->customer?->registration_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Group_Company_Name' , 'property_transaction') }}  <span class="text-danger"> *</span></label></label>
            <input type="text" class="form-control" name="group_company_name" value="{{ (isset($enquiry->customer)) ? $enquiry->customer?->group_company_name : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Contact_Person' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="contact_person" value="{{ (isset($enquiry->customer)) ? $enquiry->customer?->contact_person : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Designation'  , 'property_transaction')}}</label>
            <input type="text" class="form-control" name="designation" value="{{ (isset($enquiry->customer)) ? $enquiry->customer?->designation : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Contact_No.' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="contact_no" value="{{ (isset($enquiry->customer)) ? $enquiry->customer?->contact_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Whatsapp_No.' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="whatsapp_no" value="{{ (isset($enquiry->customer)) ? $enquiry->customer?->whatsapp_no : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Fax_No' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="fax_no" value="{{ (isset($enquiry->customer)) ? $enquiry->customer?->fax_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Telephone_No' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="telephone_no" value="{{ (isset($enquiry->customer)) ? $enquiry->customer?->telephone_no : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Other_Contact_No.' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="other_contact_no" value="{{ (isset($enquiry->customer)) ? $enquiry->customer?->other_contact_no : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Address_Line_1' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="address1" value="{{ (isset($enquiry->customer)) ? $enquiry->customer?->address1 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Address_Line_2' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="address2" value="{{ (isset($enquiry->customer)) ? $enquiry->customer?->address2 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Address_Line_3' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="address3" value="{{ (isset($enquiry->customer)) ? $enquiry->customer?->address3 : '' }}">
        </div>
    </div>


    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('City'  , 'property_transaction')}}</label>
            <input type="text" class="form-control" name="city" value="{{ (isset($enquiry->customer)) ? $enquiry->customer->city : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('State' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="state" value="{{ (isset($enquiry->customer)) ? $enquiry->customer->state : '' }}">
        </div>
    </div> 
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Email_Address' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="email1" value="{{ (isset($enquiry->customer)) ? $enquiry->customer->email1 : '' }}">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="token" class="title-color">{{ ui_change('Secondary_Email' , 'property_transaction') }}</label>
            <input type="text" class="form-control" name="email2" value="{{ (isset($enquiry->customer)) ? $enquiry->customer->email2 : '' }}">
        </div>
    </div>



</div>

