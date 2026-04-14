<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Navigation Tabs -->
                    <div class="mb-6">
                        <nav class="flex space-x-4" aria-label="Tabs">
                            <button id="customers-tab" class="tab-button px-3 py-2 font-medium text-sm rounded-md bg-blue-100 text-blue-700" data-tab="customers">
                                Customers
                            </button>
                            <button id="users-tab" class="tab-button px-3 py-2 font-medium text-sm rounded-md text-gray-500 hover:text-gray-700" data-tab="users">
                                Users
                            </button>
                        </nav>
                    </div>

                    <!-- Customers Tab Content -->
                    <div id="customers-content" class="tab-content">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Customers</h3>
                            <button id="add-customer-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add Customer
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table id="customers-table" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Users Tab Content -->
                    <div id="users-content" class="tab-content hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Users</h3>
                            <button id="add-user-btn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Add User
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table id="users-table" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Modal -->
    <div id="user-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 id="modal-title" class="text-lg font-medium text-gray-900 mb-4">Add/Edit User</h3>
                <form id="user-form">
                    <input type="hidden" id="user-id" name="id">
                    <input type="hidden" id="user-type" name="user_type" value="customer">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="firstname">First Name</label>
                        <input type="text" id="firstname" name="firstname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                        <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                        <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="status">Status</label>
                        <select id="status" name="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancel-btn" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                const tab = this.getAttribute('data-tab');

                // Update tab buttons
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('bg-blue-100', 'text-blue-700');
                    btn.classList.add('text-gray-500', 'hover:text-gray-700');
                });
                this.classList.add('bg-blue-100', 'text-blue-700');
                this.classList.remove('text-gray-500', 'hover:text-gray-700');

                // Show/hide content
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });
                document.getElementById(tab + '-content').classList.remove('hidden');

                // Load data for the tab
                loadUsers(tab);
            });
        });

        // Load users data
        async function loadUsers(userType = 'customer') {
            try {
                const response = await fetch(`/api/users?user_type=${userType}`, {
                    headers: {
                        'Authorization': `Bearer ${getToken()}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Failed to load users');

                const data = await response.json();
                const tableId = userType + 's-table';
                const tbody = document.querySelector(`#${tableId} tbody`);
                tbody.innerHTML = '';

                data.data.forEach(user => {
                    const row = `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${user.id}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${user.firstname} ${user.lastname}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${user.email}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${user.phone || ''}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${user.status == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${user.status == 1 ? 'Active' : 'Inactive'}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="editUser(${user.id}, '${userType}')" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                                <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            } catch (error) {
                console.error('Error loading users:', error);
                alert('Failed to load users');
            }
        }

        // Get auth token (you may need to adjust this based on your auth implementation)
        function getToken() {
            return localStorage.getItem('auth_token') || '';
        }

        // Modal functionality
        function openModal(userType = 'customer') {
            document.getElementById('user-type').value = userType;
            document.getElementById('modal-title').textContent = `Add ${userType.charAt(0).toUpperCase() + userType.slice(1)}`;
            document.getElementById('user-form').reset();
            document.getElementById('user-id').value = '';
            document.getElementById('user-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('user-modal').classList.add('hidden');
        }

        // Event listeners
        document.getElementById('add-customer-btn').addEventListener('click', () => openModal('customer'));
        document.getElementById('add-user-btn').addEventListener('click', () => openModal('user'));
        document.getElementById('cancel-btn').addEventListener('click', closeModal);

        // Form submission
        document.getElementById('user-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const userId = formData.get('id');
            const userType = formData.get('user_type');
            const isEdit = userId !== '';

            try {
                const url = isEdit ? `/api/users/${userId}` : `/api/users/0`;
                const method = isEdit ? 'POST' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Authorization': `Bearer ${getToken()}`,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) throw new Error('Failed to save user');

                const result = await response.json();
                alert(result.message);
                closeModal();
                loadUsers(userType);
            } catch (error) {
                console.error('Error saving user:', error);
                alert('Failed to save user');
            }
        });

        // Edit user
        async function editUser(id, userType) {
            try {
                const response = await fetch(`/api/users/${id}`, {
                    headers: {
                        'Authorization': `Bearer ${getToken()}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Failed to load user');

                const data = await response.json();
                const user = data.data;

                document.getElementById('user-id').value = user.id;
                document.getElementById('user-type').value = userType;
                document.getElementById('firstname').value = user.firstname;
                document.getElementById('lastname').value = user.lastname;
                document.getElementById('email').value = user.email;
                document.getElementById('phone').value = user.phone || '';
                document.getElementById('status').value = user.status;
                document.getElementById('password').value = ''; // Don't populate password

                document.getElementById('modal-title').textContent = `Edit ${userType.charAt(0).toUpperCase() + userType.slice(1)}`;
                document.getElementById('user-modal').classList.remove('hidden');
            } catch (error) {
                console.error('Error loading user:', error);
                alert('Failed to load user');
            }
        }

        // Delete user
        async function deleteUser(id) {
            if (!confirm('Are you sure you want to delete this user?')) return;

            try {
                const response = await fetch(`/api/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${getToken()}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Failed to delete user');

                const result = await response.json();
                alert(result.message);
                // Reload current tab
                const activeTab = document.querySelector('.tab-button.bg-blue-100').getAttribute('data-tab');
                loadUsers(activeTab);
            } catch (error) {
                console.error('Error deleting user:', error);
                alert('Failed to delete user');
            }
        }

        // Load customers by default
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers('customer');
        });
    </script>
</x-app-layout>
