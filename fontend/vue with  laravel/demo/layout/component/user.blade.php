@extends("layout-vue")
@push("css")
    <style>
        [v-cloak] {
            display: none;
        }
    </style>
@endpush
@section("content")
    <div id="root" class="container" v-cloak>
        <h1>User</h1>
        <div class="row">
            <div class="col-6 col-xxl-6 col-xl-6">
                <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#addUser"><i
                        class="fa-solid fa-user-plus"></i></button>
            </div>
            <div class="col-xxl-6 col-xl-6 col-6">
                <div class="offset-6">
                    <div class="input-group">
                        <input class="form-control" v-model="search.keySearch" />
                        <button type="button" class="btn btn-danger" v-if="search.keySearch"
                            @click="search.keySearch = ''; fetchUsers()">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <button class="btn btn-primary" type="button" @click="fetchUsers()"><i
                                class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </div>
            </div>

        </div>
        <Pagination :pagination="paginate" @change-page="fetchUsers" :show-label="true" :show-paginate="false"></Pagination>
        <table class="table table-striped mt-3">
            <tr>
                <th>ID</th>
                <th @click="changeSort('name')">Name
                    <i v-if="sortBy.name.active" :class="['fa-solid', sortBy.name.className]"></i>
                </th>
                <th>Email</th>
                <th @click="changeSort('display_created')">Change Day
                    <i v-if="sortBy.display_created.active" :class="['fa-solid', sortBy.display_created.className]"></i>
                </th>
                <th></th>
            </tr>
            <tr v-for="(user, index) in users" :key="index">
                <td>@{{ index + 1 + (paginate.meta.per_page * (paginate.meta.current_page - 1)) }}</td>
                <td>@{{ user.name }}</td>
                <td>@{{ user.email }}</td>
                <td>@{{ user.display_created }}</td>
                <td>
                    <button class="btn btn-primary" type="button" @click="openModalEdit(user)" data-bs-toggle="modal"
                        data-bs-target="#addUser"><i class="fa-regular fa-pen-to-square"></i>
                    </button>
                    <button class="btn btn-danger mx-2" type="button" @click="deleteUser(user)">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            </tr>
        </table>
        <div>
            <Pagination :pagination="paginate" @change-page="fetchUsers" :show-label="false" :show-paginate="true">
            </Pagination>
        </div>
        <div class="modal fade" id="addUser" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5"> @{{ inforModal.title }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" :class="['form-control', { 'is-invalid': inforModal.errors.name }]"
                                v-model="inforModal.body.name" name="username" placeholder="Enter username"
                                @input="clearError('name')">
                            <div class="invalid-feedback">@{{ inforModal.errors.name }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="email" :class="['form-control', { 'is-invalid': inforModal.errors.email }]"
                                v-model="inforModal.body.email" name="email" placeholder="Enter full name"
                                @input="clearError('email')">
                            <div class="invalid-feedback">@{{ inforModal.errors.email }}</div>
                        </div>

                        <div class="mb-3" v-if="!inforModal.id">
                            <label class="form-label">Password</label>
                            <input type="password" :class="['form-control', { 'is-invalid': inforModal.errors.password }]"
                                v-model="inforModal.body.password" name="password" placeholder="Enter password"
                                @input="clearError('password')">
                            <div class="invalid-feedback">@{{ inforModal.errors.password }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" @click="saveUser">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push("js")
    <script>
        const app = Vue.createApp({
            data() {
                return {
                    users: [],
                    paginate: {},
                    inforModal: {
                        title: "Add User",
                        body: {
                            email: "",
                            name: "",
                            password: ""
                        },
                        errors: {},
                        id: null
                    },
                    search: {
                        keySearch: "",
                        sortBy: {
                            display_created: "",
                            name: ""
                        },
                    },
                    sortBy: {
                        display_created: { active: false, className: '' },
                        name: { active: false, className: '' },
                    },
                }
            },
            methods: {
                async fetchUsers(url = '{{ route("user.data-list") }}') {
                    let params = _.pickBy(this.search);
                    console.log('Params:', params);
                    let res = await axios.get(url, { params });
                    this.users = res.data.data.users;
                    this.paginate = {
                        listPage: res.data.data.pagination,
                        meta: res.data.data.meta,
                    }
                },
                async saveUser() {
                    let url = this.inforModal.id ? `{{ route("user.edit", ":id") }}`.replace(":id", this.inforModal.id) : '{{ route("user.add") }}';
                    let param = _.pickBy(this.inforModal.body);
                    let res = await axios.post(url, param);
                    if (res.data.status) {
                        this.fetchUsers();
                        $("#addUser").modal("hide");
                        toastr.success(this.inforModal.id ? "Updated user successfully" : "Created user successfully");
                    } else {
                        if (res.data.errors) {
                            this.inforModal.errors = Object.fromEntries(
                                Object.entries(res.data.errors).map(([field, messages]) => [field, messages[0]])
                            );
                        }

                        toastr.error("Can not save user");
                    }
                },
                async deleteUser(user) {
                    if (confirm("Are you sure you want to delete this user?")) {
                        let url = `{{ route("user.delete", ":id") }}`.replace(":id", user.id);
                        let res = await axios.post(url);
                        if (res.data.status) {
                            this.fetchUsers();
                            toastr.success("Deleted user successfully");
                        } else {
                            toastr.error("Failed to delete user");
                        }
                    }
                },

                clearError(field) {
                    if (this.inforModal.errors[field]) {
                        this.inforModal.errors[field] = null;
                    }
                },
                openModalEdit(user) {
                    this.fillDataModal(user, "Edit User");
                },
                fillDataModal(user, title = "Add User") {
                    this.inforModal.body = {
                        email: user?.email ?? "",
                        name: user?.name ?? "",
                        password: ""
                    };
                    this.inforModal.title = title;
                    this.inforModal.errors = {};
                    this.inforModal.id = user?.id ?? null;
                },
                changeSort(column) {
                    if (this.sortBy[column].active) {
                        switch (this.sortBy[column].className) {
                            case 'fa-arrow-up':
                                this.sortBy[column].className = 'fa-arrow-down';
                                this.search.sortBy[column] = "desc"
                                break;
                            case 'fa-arrow-down':
                                this.sortBy[column].active = false;
                                this.search.sortBy[column] = ""
                                break;
                        }
                    } else {
                        this.sortBy[column].active = true;
                        this.sortBy[column].className = 'fa-arrow-up';
                        this.search.sortBy[column] = "asc"
                    }
                    this.fetchUsers();
                }
            },
            mounted() {
                this.fetchUsers();
                $("#addUser").on("hidden.bs.modal", () => {
                    this.fillDataModal();
                })
            },
        })
        app.config.globalProperties.$jquery = $;
        app.config.globalProperties.$lodash = _;
        app.config.globalProperties.$axios = axios;

        app.component('Pagination', window.GlobalComponents.PaginationComponent);
        app.mount("#root");
    </script>
@endpush