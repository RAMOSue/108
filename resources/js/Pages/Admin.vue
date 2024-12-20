<template>
    <Head title="Admin Dashboard" />
    <AuthenticatedLayout>
        <div class="flex flex-col lg:flex-row min-h-screen bg-gray-100">
            <!-- Sidebar -->
            <aside class="w-full lg:w-64 bg-white shadow-md">
                <div class="p-6 flex flex-col items-center">
                    <img
                        :src="`https://ui-avatars.com/api/?name=${$page.props.auth.user.name}&background=random`"
                        alt="User Avatar"
                        class="w-24 h-24 rounded-full mb-4 border-4 border-green-500 shadow-lg"
                    />
                    <h2 class="text-xl font-bold text-gray-800">
                        {{ $page.props.auth.user.name }}
                    </h2>
                    <p class="text-sm text-gray-600 mb-2">
                        {{ $page.props.auth.user.email }}
                    </p>
                    <p
                        class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold"
                    >
                        {{ getRole($page.props.auth.user.role_id) }}
                    </p>
                </div>
                <nav class="mt-6">
                    <a
                        v-for="item in navItems"
                        :key="item.name"
                        :href="item.href"
                        class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200"
                    >
                        <component :is="item.icon" class="h-5 w-5 mr-3" />
                        {{ item.name }}
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main
                class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 custom-scrollbar"
            >
                <div class="container mx-auto px-4 lg:px-6 py-8">
                    <h1 class="text-4xl font-bold text-gray-800 mb-8">
                        Admin Dashboard
                    </h1>

                    <!-- Dashboard Stats -->
                    <section
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12 px-4"
                    >
                        <div
                            v-for="stat in computedStatistics"
                            :key="stat.title"
                            class="bg-gradient-to-r from-blue-100 to-green-100 p-6 rounded-2xl shadow-xl hover:scale-105 hover:shadow-2xl transition-all duration-300"
                        >
                            <div class="flex items-center justify-between mb-5">
                                <h3
                                    class="text-xl font-medium text-gray-700 capitalize"
                                >
                                    {{ stat.title }}
                                </h3>
                                <component
                                    :is="stat.icon"
                                    class="h-10 w-10 text-gray-600"
                                />
                            </div>
                            <p class="text-5xl font-extrabold text-gray-900">
                                {{ stat.value }}
                            </p>
                        </div>
                    </section>

                    <!-- Staff Recent Tribe -->
                    <section id="recent-tribes" class="mb-12 px-4">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-3xl font-bold text-gray-800">
                                Recent Staff Tribe
                            </h2>
                        </div>
                        <div
                            class="bg-white rounded-2xl shadow-lg overflow-hidden"
                        >
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr
                                            class="bg-gradient-to-r from-blue-50 to-green-50 text-left font-semibold text-gray-700 uppercase tracking-wider"
                                        >
                                            <th class="px-6 py-4">
                                                Staff Name
                                            </th>
                                            <th class="px-6 py-4">
                                                Recent Tribe
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr
                                            v-for="staff in filteredStaffs"
                                            :key="staff.id"
                                            class="hover:bg-gray-50"
                                        >
                                            <td
                                                class="px-6 py-4 whitespace-nowrap"
                                            >
                                                <div class="flex items-center">
                                                    <div
                                                        class="flex-shrink-0 h-12 w-12"
                                                    >
                                                        <img
                                                            class="h-12 w-12 rounded-full ring-2 ring-blue-300"
                                                            :src="`https://ui-avatars.com/api/?name=${staff.name}&background=random`"
                                                            :alt="staff.name"
                                                        />
                                                    </div>
                                                    <div class="ml-4">
                                                        <div
                                                            class="text-lg font-medium text-gray-900"
                                                        >
                                                            {{ staff.name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-gray-600"
                                            >
                                                {{ staff.recent_tribe }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    <!-- User Management -->
                    <section id="users" class="mb-12">
                        <div
                            class="flex flex-col sm:flex-row justify-between items-center mb-6"
                        >
                            <h2
                                class="text-3xl font-bold text-gray-800 mb-4 sm:mb-0"
                            >
                                User Management
                            </h2>
                            <button
                                @click="showAddUserModal = true"
                                class="w-full sm:w-auto bg-blue-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-700 transition-colors duration-300 flex items-center justify-center shadow-lg"
                            >
                                <PlusIcon class="h-5 w-5 mr-2" />
                                Add User
                            </button>
                        </div>
                        <div class="grid grid-cols-1 gap-6 mb-10">
                            <div
                                v-for="role in ['admin', 'staff', 'viewer']"
                                :key="role"
                                class="bg-white p-4 rounded-lg shadow-md overflow-x-auto"
                            >
                                <h2
                                    class="text-2xl font-bold text-gray-800 mb-4 capitalize"
                                >
                                    {{ role }}s
                                </h2>
                                <table
                                    class="min-w-full divide-y divide-gray-200"
                                >
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                No.
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                Name
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                Email
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                Role
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-gray-200"
                                    >
                                        <tr
                                            v-for="(
                                                user, index
                                            ) in groupedUsers[role]"
                                            :key="user.id"
                                        >
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap"
                                            >
                                                <div class="flex items-center">
                                                    <div
                                                        class="flex-shrink-0 h-10 w-10"
                                                    >
                                                        <img
                                                            class="h-10 w-10 rounded-full"
                                                            :src="`https://ui-avatars.com/api/?name=${user.name}&background=random`"
                                                            alt=""
                                                        />
                                                    </div>
                                                    <div class="ml-4">
                                                        <div
                                                            class="text-sm font-medium text-gray-900"
                                                        >
                                                            {{ user.name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                            >
                                                {{ user.email }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap"
                                            >
                                                <span
                                                    :class="{
                                                        'bg-green-100 text-green-800':
                                                            getRole(
                                                                user.role_id
                                                            ) === 'Admin',
                                                        'bg-blue-100 text-blue-800':
                                                            getRole(
                                                                user.role_id
                                                            ) === 'Staff',
                                                        'bg-yellow-100 text-yellow-800':
                                                            getRole(
                                                                user.role_id
                                                            ) === 'User',
                                                    }"
                                                    class="px-3 py-1 rounded-full text-xs font-medium"
                                                >
                                                    {{ getRole(user.role_id) }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                            >
                                                <button
                                                    v-if="user.role_id !== 1"
                                                    @click="editUser(user)"
                                                    class="text-blue-600 hover:text-blue-900 mr-3 transition-colors duration-300"
                                                >
                                                    <PencilIcon
                                                        class="h-5 w-5"
                                                    />
                                                </button>
                                                <button
                                                    v-if="user.role_id !== 1"
                                                    @click="deleteUser(user)"
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-300"
                                                >
                                                    <TrashIcon
                                                        class="h-5 w-5"
                                                    />
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    <!-- Tribe Overview -->
                    <section id="tribes" class="mb-12">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-3xl font-bold text-gray-800">
                                Tribe Overview
                            </h2>
                        </div>
                        <div
                            class="bg-white rounded-xl shadow-md overflow-hidden"
                        >
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full divide-y divide-gray-200"
                                >
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                Tribe Name
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                Category
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                Staff
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                Date Created
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-gray-200"
                                    >
                                        <tr
                                            v-for="tribe in sortedTribes"
                                            :key="tribe.tribe_id"
                                            class="hover:bg-gray-50"
                                        >
                                            <td
                                                class="px-6 py-4 whitespace-nowrap"
                                            >
                                                {{ tribe.tribe_name }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap"
                                            >
                                                <span
                                                    :class="
                                                        getCategoryClass(
                                                            tribe.category_name
                                                        )
                                                    "
                                                    class="px-3 py-1 rounded-full text-xs font-medium"
                                                >
                                                    {{ tribe.category_name }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap"
                                            >
                                                {{ tribe.staff_name }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap"
                                            >
                                                {{
                                                    formatDate(tribe.created_at)
                                                }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                            >
                                                <button
                                                    @click="deleteTribe(tribe)"
                                                    class="text-red-600 hover:text-red-900 transition-colors ms-5 duration-300"
                                                >
                                                    <TrashIcon
                                                        class="h-5 w-5"
                                                    />
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    <!-- Activity Logs -->
                    <section id="activity" class="mb-12">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-3xl font-bold text-gray-800">
                                Activity Logs
                            </h2>
                            <button
                                @click="refreshActivityLogs"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-blue-700 transition-colors duration-300 flex items-center shadow-lg"
                            >
                                <ArrowPathIcon class="h-5 w-5 mr-2" />
                                Refresh Logs
                            </button>
                        </div>

                        <!-- Filtering Options -->
                        <div class="flex space-x-4 mb-6">
                            <div class="flex items-center space-x-2">
                                <label
                                    for="actorFilter"
                                    class="text-sm font-medium text-gray-700"
                                    >Actor:</label
                                >
                                <input
                                    type="text"
                                    id="actorFilter"
                                    v-model="filters.actor"
                                    class="px-3 py-2 border rounded-md text-sm"
                                    placeholder="Search by actor"
                                />
                            </div>

                            <div class="flex items-center space-x-2">
                                <label
                                    for="actionFilter"
                                    class="text-sm font-medium text-gray-700"
                                    >Action:</label
                                >
                                <input
                                    type="text"
                                    id="actionFilter"
                                    v-model="filters.action"
                                    class="px-3 py-2 border rounded-md text-sm"
                                    placeholder="Search by action"
                                />
                            </div>

                            <div class="flex items-center space-x-2">
                                <label
                                    for="dateFilter"
                                    class="text-sm font-medium text-gray-700"
                                    >Date:</label
                                >
                                <input
                                    type="date"
                                    id="dateFilter"
                                    v-model="filters.date"
                                    class="px-3 py-2 border rounded-md text-sm"
                                />
                            </div>
                        </div>

                        <div
                            class="bg-white rounded-xl shadow-md overflow-hidden"
                        >
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr
                                            class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                        >
                                            <th class="px-6 py-3">Actor</th>
                                            <th class="px-6 py-3">Role</th>
                                            <th class="px-6 py-3">Action</th>
                                            <th class="px-6 py-3">
                                                Modified Item
                                            </th>
                                            <th class="px-6 py-3">
                                                Table Name
                                            </th>
                                            <th class="px-6 py-3">Created</th>
                                            <th class="px-6 py-3">Updated</th>
                                            <th class="px-6 py-3">Deleted</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr
                                            v-for="log in filteredLogs"
                                            :key="log.log_id"
                                            class="hover:bg-gray-50"
                                        >
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0">
                                                        <component
                                                            :is="
                                                                getActivityIcon(
                                                                    log.action
                                                                )
                                                            "
                                                            class="h-6 w-6 text-gray-500"
                                                        />
                                                    </div>
                                                    <div class="ml-4">
                                                        <p
                                                            class="text-sm font-medium text-gray-900"
                                                        >
                                                            {{ log.user_name }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                            >
                                                {{ log.role_user }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                            >
                                                {{ log.action }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                            >
                                                {{ log.affected_name }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                            >
                                                {{ log.table_name }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                            >
                                                {{ formatDate(log.created_at) }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                            >
                                                {{
                                                    log.updated_at
                                                        ? formatDate(
                                                              log.updated_at
                                                          )
                                                        : "N/A"
                                                }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                            >
                                                {{
                                                    log.deleted_at
                                                        ? formatDate(
                                                              log.deleted_at
                                                          )
                                                        : "N/A"
                                                }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
        </div>

        <!-- Confirmation Modal -->
        <TransitionRoot appear :show="showConfirmModal" as="template">
            <Dialog as="div" @close="closeConfirmModal" class="relative z-50">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black/75" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div
                        class="flex min-h-full items-center justify-center p-4 text-center"
                    >
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                            >
                                <DialogTitle
                                    as="h3"
                                    class="text-lg font-medium leading-6 text-gray-900 mb-4"
                                >
                                    {{ confirmModalConfig.title }}
                                </DialogTitle>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        {{ confirmModalConfig.message }}
                                    </p>
                                </div>

                                <div class="mt-6 flex justify-end space-x-3">
                                    <button
                                        @click="closeConfirmModal"
                                        class="inline-flex justify-center rounded-md px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        @click="confirmAction"
                                        class="inline-flex justify-center rounded-md px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                    >
                                        Confirm
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>

        <!-- Add/Edit User Modal -->
        <TransitionRoot appear :show="showAddUserModal" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-50">
                <div class="fixed inset-0 overflow-y-auto">
                    <div
                        class="flex min-h-full items-center justify-center p-4 text-center"
                    >
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                            >
                                <DialogTitle
                                    as="h3"
                                    class="text-lg font-medium leading-6 text-gray-900 mb-4"
                                >
                                    {{
                                        editingUser
                                            ? "Edit User"
                                            : "Add New User"
                                    }}
                                </DialogTitle>
                                <form
                                    @submit.prevent="submitUser"
                                    class="mt-4 space-y-4"
                                >
                                    <div>
                                        <label
                                            for="name"
                                            class="block text-sm font-medium text-gray-700"
                                            >Name</label
                                        >
                                        <input
                                            type="text"
                                            id="name"
                                            v-model="userForm.name"
                                            required
                                            :disabled="editingUser"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            placeholder="Enter name"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            for="email"
                                            class="block text-sm font-medium text-gray-700"
                                            >Email</label
                                        >
                                        <input
                                            type="email"
                                            id="email"
                                            v-model="userForm.email"
                                            required
                                            :disabled="editingUser"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            placeholder="Enter email"
                                        />
                                    </div>

                                    <!-- Only show password field for adding a new user -->
                                    <div v-if="!editingUser">
                                        <label
                                            for="password"
                                            class="block text-sm font-medium text-gray-700"
                                            >Password</label
                                        >
                                        <input
                                            type="password"
                                            id="password"
                                            v-model="userForm.password"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            placeholder="Enter password"
                                        />
                                    </div>

                                    <!-- Role Selection Dropdown -->
                                    <div>
                                        <label
                                            for="role"
                                            class="block text-sm font-medium text-gray-700"
                                            >Role</label
                                        >
                                        <select
                                            id="role"
                                            v-model="userForm.role_id"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        >
                                            <option value="" disabled>
                                                Select a role
                                            </option>
                                            <option
                                                v-for="role in roles"
                                                :key="role.id"
                                                :value="role.id"
                                            >
                                                {{ role.role_user }}
                                            </option>
                                        </select>
                                    </div>

                                    <div
                                        class="mt-6 flex justify-end space-x-3"
                                    >
                                        <button
                                            type="button"
                                            @click="closeModal"
                                            class="inline-flex justify-center rounded-md border border-transparent bg-gray-200 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            type="submit"
                                            class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                        >
                                            {{
                                                editingUser
                                                    ? "Update User"
                                                    : "Add User"
                                            }}
                                        </button>
                                    </div>
                                </form>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </AuthenticatedLayout>

    <!-- Creative Alert -->
    <TransitionRoot appear :show="showCreativeAlert" as="template">
        <Dialog as="div" @close="closeCreativeAlert" class="relative z-50">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black bg-opacity-25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div
                    class="flex min-h-full items-center justify-center p-4 text-center"
                >
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                        >
                            <div class="flex items-center justify-center mb-4">
                                <div
                                    :class="
                                        creativeAlertConfig.isError
                                            ? 'bg-red-100'
                                            : 'bg-green-100'
                                    "
                                    class="rounded-full p-3"
                                >
                                    <component
                                        :is="
                                            creativeAlertConfig.isError
                                                ? XMarkIcon
                                                : CheckIcon
                                        "
                                        :class="
                                            creativeAlertConfig.isError
                                                ? 'text-red-600'
                                                : 'text-green-600'
                                        "
                                        class="h-6 w-6"
                                    />
                                </div>
                            </div>
                            <DialogTitle
                                as="h3"
                                class="text-lg font-medium leading-6 text-gray-900 text-center mb-2"
                            >
                                {{ creativeAlertConfig.title }}
                            </DialogTitle>
                            <p class="text-sm text-gray-500 text-center mb-4">
                                {{ creativeAlertConfig.message }}
                            </p>
                            <div class="mt-4 flex justify-center">
                                <button
                                    type="button"
                                    :class="
                                        creativeAlertConfig.isError
                                            ? 'bg-red-100 text-red-900 hover:bg-red-200'
                                            : 'bg-blue-100 text-blue-900 hover:bg-blue-200'
                                    "
                                    class="inline-flex justify-center rounded-md border border-transparent px-4 py-2 text-sm font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 transition-colors duration-300"
                                    @click="closeCreativeAlert"
                                >
                                    {{
                                        creativeAlertConfig.isError
                                            ? "Close"
                                            : "Got it, thanks!"
                                    }}
                                </button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { defineProps } from "vue";
import { ref, computed } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import {
    ChartBarIcon,
    ClockIcon,
    ArrowPathIcon,
    UserGroupIcon,
    PlusIcon,
    PencilIcon,
    TrashIcon,
    XMarkIcon,
    UserIcon,
    ShieldCheckIcon,
    BookOpenIcon,
    CheckIcon,
    GlobeAltIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    statistics: {
        type: Array,
        default: () => ({
            total_users: 0,
            total_tribes: 0,
            total_staffs: 0,
            total_admins: 0,
        }),
    },
    users: {
        type: Array,
        default: () => [],
    },
    roles: {
        type: Array,
        default: () => [],
    },
    tribes: {
        type: Array,
        default: () => [],
    },
    recentTribes: {
        type: Array,
        default: () => [],
    },
    logs: {
        type: Array,
        default: () => [],
    },
});

const filters = ref({
    actor: "",
    action: "",
    date: "",
});

const filteredLogs = computed(() => {
    return props.logs.filter((log) => {
        const actorMatch = log.user_name
            .toLowerCase()
            .includes(filters.value.actor.toLowerCase());
        const actionMatch = log.action
            .toLowerCase()
            .includes(filters.value.action.toLowerCase());
        const dateMatch = filters.value.date
            ? formatDate(log.created_at) === filters.value.date
            : true;

        return actorMatch && actionMatch && dateMatch;
    });
});

const getActivityIcon = (type) => {
    switch (type) {
        case "user":
            return UserIcon;
        case "tribes":
            return GlobeAltIcon;
        default:
            return ClockIcon;
    }
};

const filteredStaffs = computed(() => {
    const recentTribesMap = props.recentTribes.reduce((map, tribe) => {
        map[tribe.user_id] = tribe.recent_tribe;
        return map;
    }, {});

    return props.users
        .filter((user) => user.role.id === 2)
        .map((staff) => ({
            ...staff,
            recent_tribe:
                recentTribesMap[staff.id] || "No recent tribes available",
        }));
});

const tribes = ref(props.tribes);

const sortedTribes = computed(() => {
    return [...tribes.value].sort((a, b) => a.category_id - b.category_id);
});

const deleteTribe = (tribe) => {
    showConfirmModal.value = true;
    confirmModalConfig.value = {
        title: "Delete Tribe",
        message: `Are you sure you want to delete the tribe "${tribe.tribe_name}"?`,
        onConfirm: () => {
            router.delete(`/admin/tribes/${tribe.tribe_id}`, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    tribes.value = tribes.value.filter(
                        (r) => r.tribe_id !== tribe.tribe_id
                    );
                    showCreativeAlert.value = true;
                    creativeAlertConfig.value = {
                        title: "Tribe Deleted Successfully!",
                        message: `The tribe "${tribe.tribe_name}" has been deleted.`,
                        isError: false,
                    };
                },
                onError: (error) => {
                    console.error("Error deleting tribe:", error);
                    showCreativeAlert.value = true;
                    creativeAlertConfig.value = {
                        title: "Error",
                        message: "Failed to delete tribe. Please try again.",
                        isError: true,
                    };
                },
            });
        },
    };
};

const computedStatistics = computed(() => [
    {
        title: "Total Users",
        value: props.statistics[0].total_users,
        icon: UserGroupIcon,
    },
    {
        title: "Total Tribes",
        value: props.statistics[0].total_tribes,
        icon: GlobeAltIcon,
    },
    {
        title: "Total Staffs",
        value: props.statistics[0].total_staffs,
        icon: UserGroupIcon,
    },
    {
        title: "Total Admins",
        value: props.statistics[0].total_admins,
        icon: ShieldCheckIcon,
    },
]);

const activityLogs = ref(props.activityLogs);
const showAddUserModal = ref(false);
const editingUser = ref(null);
const showCreativeAlert = ref(false);
const creativeAlertConfig = ref({
    title: "",
    message: "",
    isError: false,
});

const userForm = ref({
    name: "",
    email: "",
    password: "",
    role_id: null,
});

const showConfirmModal = ref(false);
const confirmModalConfig = ref({
    title: "",
    message: "",
    onConfirm: null,
});

const navItems = [
    { name: "Dashboard", href: "#statistics", icon: ChartBarIcon },
    { name: "Users", href: "#users", icon: UserGroupIcon },
    { name: "Tribes", href: "#tribes", icon: GlobeAltIcon },
    { name: "Activity", href: "#activity", icon: ClockIcon },
];

const groupedUsers = computed(() => {
    const grouped = {
        admin: [],
        staff: [],
        viewer: [],
    };
    props.users.forEach((user) => {
        const role = getRole(user.role_id).toLowerCase();
        if (grouped[role]) {
            grouped[role].push(user);
        }
    });
    return grouped;
});

const getRole = (role_id) => {
    const role = props.roles.find((r) => r.id === role_id);
    return role ? role.role_user : "Unknown";
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0"); // Months are zero-based
    const day = String(date.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
};

const closeConfirmModal = () => {
    showConfirmModal.value = false;
    confirmModalConfig.value = {
        title: "",
        message: "",
        onConfirm: null,
    };
};

const confirmAction = () => {
    if (confirmModalConfig.value.onConfirm) {
        confirmModalConfig.value.onConfirm();
    }
    closeConfirmModal();
};

const deleteUser = (user) => {
    showConfirmModal.value = true;
    confirmModalConfig.value = {
        title: "Delete User",
        message: `Are you sure you want to delete the user "${user.name}"?`,
        onConfirm: () => {
            router.delete(route("admin.deleteUser", { user: user.id }), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    props.users = props.users.filter((u) => u.id !== user.id);
                    showCreativeAlert.value = true;
                    creativeAlertConfig.value = {
                        title: "User Deleted Successfully!",
                        message: `${getRole(user.role_id)} "${
                            user.name
                        }" has been deleted.`,
                        isError: false,
                    };
                },
                onError: (error) => {
                    console.error("Error deleting user:", error);
                    showCreativeAlert.value = true;
                    creativeAlertConfig.value = {
                        title: "Error",
                        message: "Unable to delete the user. Please try again.",
                        isError: true,
                    };
                },
            });
        },
    };
};

const editUser = (user) => {
    if (getRole(user.role_id) === "Admin") {
        console.error("Cannot edit Admin user");
        return;
    }

    showAddUserModal.value = true;
    editingUser.value = user;
    userForm.value = {
        id: user.id,
        name: user.name,
        email: user.email,
        role_id: user.role_id,
    };
};

const submitUser = () => {
    if (editingUser.value) {
        if (
            getRole(editingUser.value.role_id) === "Admin" &&
            userForm.value.role_id !== editingUser.value.role_id
        ) {
            console.error("Cannot demote Admin user");
            return;
        }

        router.post(
            route("admin.updateRole", { id: userForm.value.id }),
            {
                role_id: userForm.value.role_id,
            },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (response) => {
                    console.log("User updated successfully:", response.data);

                    // Update the user in the local list after successful update
                    const updatedUser = response.data.user;
                    const userIndex = props.users.findIndex(
                        (u) => u.id === updatedUser.id
                    );
                    if (userIndex > -1) {
                        props.users[userIndex] = updatedUser;
                    }

                    closeModal();
                    showCreativeAlert.value = true;
                    creativeAlertConfig.value = {
                        title: "User Updated",
                        message: `${
                            updatedUser.name
                        } has been successfully updated to ${getRole(
                            updatedUser.role_id
                        )}.`,
                        isError: false,
                    };
                },
                onError: (errors) => {
                    console.error("Error updating user:", errors);
                    showCreativeAlert.value = true;
                    creativeAlertConfig.value = {
                        title: "Error",
                        message:
                            errors.message ||
                            "There was an error updating the user. Please try again.",
                        isError: true,
                    };
                },
            }
        );
    } else {
        // Handle user creation logic (unchanged)
        router.post(route("admin.addUser"), userForm.value, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                const updatedUsers = [...props.users, { ...userForm.value }];
                props.users = updatedUsers;
                closeModal();
                showCreativeAlert.value = true;
                creativeAlertConfig.value = {
                    title: "User Added Successfully!",
                    message: `New user has been added.`,
                    isError: false,
                };
            },
            onError: (errors) => {
                showCreativeAlert.value = true;
                creativeAlertConfig.value = {
                    title: "Error",
                    message:
                        "There was an error adding the user: " +
                        Object.values(errors).join(", "),
                    isError: true,
                };
            },
        });
    }
};

const closeModal = () => {
    showAddUserModal.value = false;
    editingUser.value = null;
    userForm.value = { id: "", name: "", email: "", password: "", role_id: "" };
};

const refreshActivityLogs = () => {
    router.post(
        route("admin.refreshActivityLogs"),
        {},
        {
            onSuccess: () => {
                showCreativeAlert.value = true;
                creativeAlertConfig.value = {
                    title: "Logs Refreshed",
                    message: "Activity logs have been successfully refreshed.",
                    isError: false,
                };
            },
            onError: (error) => {
                console.error("Error refreshing activity logs:", error);
                showCreativeAlert.value = true;
                creativeAlertConfig.value = {
                    title: "Error",
                    message:
                        "Unable to refresh activity logs. Please try again.",
                    isError: true,
                };
            },
        }
    );
};

const getCategoryClass = (category) => {
    switch (category.toLowerCase()) {
        case "main course":
            return "bg-orange-100 text-orange-800";
        case "appetizer":
            return "bg-yellow-100 text-yellow-800";
        case "dessert":
            return "bg-pink-100 text-pink-800";
        case "beverages":
            return "bg-blue-100 text-blue-800";
        default:
            return "bg-gray-100 text-gray-800";
    }
};

const closeCreativeAlert = () => {
    showCreativeAlert.value = false;
};
</script>

<style scoped>
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #4f46e5 #e5e7eb;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #e5e7eb;
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #4f46e5;
    border-radius: 4px;
    border: 2px solid #e5e7eb;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: #4338ca;
}
</style>
