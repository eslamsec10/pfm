<div class="modal fade" id="add_customer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            {{ ui_change('create_customer', 'property_transaction') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card-body">
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                <li class="nav-item">
                                    <a class="nav-link type_link_create active" href="#"
                                        id="personal-link_create">{{ ui_change('personal', 'property_transaction') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link type_link_create " href="#"
                                        id="company-link_create">{{ ui_change('company', 'property_transaction') }}</a>
                                </li>
                            </ul>
                            <div class="col-md-12 customer_form_create personal-form_create" id="personal-form_create">
                                <form id="customerForm_personal" action="{{ route('sales.customer.store_for_anything') }}"
                                    method="post" class="customerForm">
                                    @csrf
                                    @method('post')
                                    @include('admin-views.sales.customer.personal_form')
                                    <div class="row justify-content-end gap-3 mt-3 mx-1">
                                        <button type="reset"
                                            class="btn btn-secondary px-5">{{ ui_change('reset', 'property_transaction') }}</button>
                                        <button type="submit" id="saveTenantPersonal"
                                            class="btn btn--primary px-5 saveTenant">{{ ui_change('submit', 'property_transaction') }}</button>
                                    </div>

                                </form>
                            </div>
                            <div class="col-md-12 customer_form_create d-none company-form_create" id="company-form_create">
                                <form id="customerForm_company" action="{{ route('sales.customer.store_for_anything') }}"
                                    method="post" class="customerForm">
                                    @csrf
                                    @method('post')

                                    @include('admin-views.sales.customer.company_form')
                                    <div class="row justify-content-end gap-3 mt-3 mx-1">
                                        <button type="reset"
                                            class="btn btn-secondary px-5">{{ ui_change('reset', 'property_transaction') }}</button>
                                        <button type="submit" id="saveTenantCompany"
                                            class="btn btn--primary px-5 saveTenant">{{ ui_change('submit', 'property_transaction') }}</button>
                                    </div>

                                </form>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>