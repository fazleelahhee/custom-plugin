const $jq = jQuery || undefined;

declare const ajaxurl:string;
declare const ajaxnonce:string;

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
        let thead = '';
        let tbody = '';
        data.field_display.forEach((field:any) => {
            thead += `<th scope="col">${field.label}</th>`;
        });

        if (data.data.length) {
            data.data.forEach((user:any) => {
                tbody += `   <tr>`;
                data.field_display.forEach((field:any) => {
                    if (field.link === 'y') {
                        tbody += `<td><a href="#" class="wpcp-view-user" data-user-id="${user.id}">${user[field.key]}</a></th>`;
                    } else {
                        tbody += `<td>${user[field.key]}</th>`;
                    }

                });
                tbody += `   </tr>`;
            });
        } else {
            tbody += `           <tr>
                                  <th scope="row" colspan="${data.field_display.length}">No User found!</th>
                                </tr>`;
        }

        this.html = `<h4>List of Users</h4>
                        <table class="table">
                              <thead>
                                <tr>
                                 ${thead}
                                </tr>
                              </thead>
                              <tbody>
                                ${tbody}
                              </tbody>
                            </table>
                            <div class="wpcp-single-user"></div>`;;
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
        let tmp = '';
        data.field_display.forEach((field:any) => {
            tmp += `<div class="row">
                            <div class="col text-right mr-2 font-weight-bold">${field.label}</div>
                            <div class="col ml-2 font-weight-light">${data.data[field.key]}</div>
                        </div>`
        });
        this.html = `<div class="container pt-5">
                        <h4 class="text-center">Selected User ID: ${data.data.id} </h4>
                        ${tmp}
                   </div>`;
    }
}


$jq(($) => {
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