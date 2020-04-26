const $jq = jQuery || undefined;

declare const ajaxurl:string;

interface IBase {
    element: any;
    html: string;
    init(config?:any): any;
    render(): any;
}

class Base implements IBase {
    element:any;
    html:string = "";

    constructor(element:any) {
        this.element = element;
    }

    init(config?:any):any {
    }

    render():any {
        this.element.html(this.html);
    }
}

class AjaxLoader extends Base {
    init():any {
        this.html = `<div class="ajax-loader">
                           <p><strong>loading ... </strong></p>
                      </div>`;
        this.render();
        return this;
    }
}

class UserCollection extends Base {
    init():any {
        const loader = new AjaxLoader(this.element);
        loader.init();

        fetch(`${ajaxurl}?action=wpcplugin_user_collection`)
            .then((response) => {
                if (response.status !== 200) {
                    let errorMsg = `Looks like there was a problem. Status Code: ${response.status}`;
                    console.log(errorMsg);
                    this.html = `<p>${errorMsg}</p>`;
                    this.render();
                    return;
                }

                response.json().then((data) => {
                    this.template(data);
                    this.render();
                });
            }
        ).catch(function (err) {
                this.html = `<p>Something went wrong.</p>`;
                this.render();
            });
        return this;
    }

    template(data:any) {
        let html = `<h4>List of Users</h4>
                        <table class="table">
                              <thead>
                                <tr> `;
        data.field_display.forEach((field:any) => {
            html += `<th scope="col">${field.label}</th>`;
        });
        html += `       </tr>
                              </thead>
                              <tbody>`;
        if (data.data.length) {
            data.data.forEach((user:any) => {
                html += `   <tr>`;
                data.field_display.forEach((field:any) => {
                    if (field.link === 'y') {
                        html += `<td><a href="#" class="wpcp-view-user" data-user-id="${user.id}">${user[field.key]}</a></th>`;
                    } else {
                        html += `<td>${user[field.key]}</th>`;
                    }

                });
                html += `   </tr>`;
            });
        } else {
            html += `           <tr>
                                  <th scope="row" colspan="${data.field_display.length}">No User found!</th>
                                </tr>`;
        }

        html += `
                              </tbody>
                            </table>
                            <div class="wpcp-single-user"></div>
                            `;

        this.html = html;
    }
}


class User extends Base {
    init(userId:number):any {
        const loader = new AjaxLoader(this.element);
        loader.init();

        fetch(`${ajaxurl}?action=wpcplugin_user&user_id=${userId}`)
            .then((response) => {
                if (response.status !== 200) {
                    let errorMsg = `Looks like there was a problem. Status Code: ${response.status}`;
                    console.log(errorMsg);
                    this.html = `<p>${errorMsg}</p>`;
                    this.render();
                    return;
                }

                response.json().then((data) => {
                    this.template(data.data);
                    this.render();
                });
            }
        ).catch(function (err) {
                this.html = `<p>Something went wrong.</p>`;
                this.render();
            });
        return this;
    }

    template(data:any) {
        let html = `<div class="container pt-5">
                        <h4 class="text-center">Selected User ID: ${data.id} </h4>
                        <div class="row">
                            <div class="col text-right mr-2 font-weight-bold">Name</div>
                            <div class="col ml-2 font-weight-light">${data.name}</div>
                        </div>

                        <div class="row">
                            <div class="col text-right mr-2 font-weight-bold">Email</div>
                            <div class="col ml-2 font-weight-light">${data.email}</div>
                        </div>

                        <div class="row">
                            <div class="col text-right mr-2 font-weight-bold">Username</div>
                            <div class="col ml-2 font-weight-light">${data.username}</div>
                        </div>

                        <div class="row">
                            <div class="col text-right mr-2 font-weight-bold">Telephone</div>
                            <div class="col ml-2 font-weight-light">${data.phone}</div>
                        </div>

                        <div class="row">
                            <div class="col text-right mr-2 font-weight-bold">Website</div>
                            <div class="col ml-2 font-weight-light"><a href="${data.website}">${data.website}</a></div>
                        </div>

                        <div class="row">
                            <div class="col text-right mr-2 font-weight-bold">Address</div>
                            <div class="col ml-2 font-weight-light">
                                ${data.address.street} <br />
                                ${data.address.suite} <br />
                                ${data.address.city} <br />
                                ${data.address.zipcode} <br />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col text-right mr-2 font-weight-bold">Company</div>
                            <div class="col ml-2 font-weight-light">${data.company.name}</div>
                        </div>
                    </div>`;
        this.html = html;
    }
}


$jq(document).ready(function ($) {
    /**
     * Load user list when page load
     * @type {Users}
     */
    const users = new UserCollection($(".wpc-plugin"));
    users.init();


    $('.wpc-plugin').on('click', '.wpcp-view-user', function (e) {
        e.preventDefault();

        /**
         * Load single user when click on the selector. and displaying under the user list.
         *
         * @type {User}
         */
        const user = new User($('.wpcp-single-user'));
        user.init(parseInt($(this).data('user-id')));
    });
});