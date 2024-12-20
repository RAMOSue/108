<template>
    <AuthenticatedLayout>
        <Head :title="`Welcome, ${$page.props.auth.user.name}`" />
        <div class="min-h-screen bg-gradient-to-b from-blue-50 to-gray-100">
            <!-- Main Content -->
            <main class="container mx-auto px-6 py-10">
                <div class="bg-white shadow-xl rounded-2xl p-10">
                    <header class="text-center mb-8">
                        <h2 class="text-4xl font-bold text-gray-800 mb-4">
                            Welcome to Your Tribo Dashboard!
                        </h2>
                        <p class="text-lg text-gray-600">
                            Ready to
                            {{
                                getWelcomeMessage(
                                    getRole($page.props.auth.user.role_id)
                                )
                            }}?
                        </p>
                    </header>

                    <!-- Feature Cards -->
                    <section
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8"
                    >
                        <div
                            v-for="(feature, index) in getRoleFeatures(
                                $page.props.auth.user.role_id
                            )"
                            :key="index"
                            class="bg-gradient-to-r from-blue-100 to-green-100 p-6 rounded-2xl shadow-xl hover:scale-105 hover:shadow-2xl transition-all duration-300"
                        >
                            <div class="flex flex-col items-center text-center">
                                <!-- Icon with Background -->
                                <div
                                    class="p-4 bg-white rounded-full shadow-lg mb-4"
                                >
                                    <component
                                        :is="feature.icon"
                                        class="h-10 w-10 text-blue-500"
                                    />
                                </div>

                                <!-- Title -->
                                <h3
                                    class="text-lg font-medium text-gray-800 capitalize mb-2"
                                >
                                    {{ feature.title }}
                                </h3>

                                <!-- Description -->
                                <p class="text-sm text-gray-600">
                                    {{ feature.description }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Action Button -->
                    <div class="mt-12 text-center">
                        <Link
                            :href="
                                getDashboardRoute($page.props.auth.user.role_id)
                            "
                            class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-md hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300 ease-in-out"
                        >
                            <component
                                :is="
                                    getDashboardIcon(
                                        $page.props.auth.user.role_id
                                    )
                                "
                                class="mr-2 h-5 w-5"
                            />
                            {{
                                getDashboardButtonText(
                                    $page.props.auth.user.role_id
                                )
                            }}
                        </Link>
                    </div>
                </div>

                <!-- Quick Stats Section -->
                <section class="mt-12">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">
                        Quick Stats
                    </h3>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8"
                    >
                        <div
                            v-for="(stat, index) in getQuickStats(
                                $page.props.auth.user.role_id
                            )"
                            :key="index"
                            class="p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">
                                        {{ stat.title }}
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-800"
                                    >
                                        {{ stat.value }}
                                    </p>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <component
                                        :is="stat.icon"
                                        class="h-6 w-6 text-blue-500"
                                    />
                                </div>
                            </div>
                            <div class="mt-4 flex items-center">
                                <span
                                    class="text-green-500 mr-2 flex items-center"
                                >
                                    <component
                                        :is="stat.trendIcon"
                                        class="h-4 w-4"
                                    />
                                    <span class="text-sm font-medium">
                                        {{ stat.change }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import {
    Users,
    Book,
    Activity,
    Star,
    TrendingUp,
    ClipboardList,
    UserPlus,
    FileText,
    Eye,
    Search,
    Heart,
    Award,
    ThumbsUp,
    MessageCircle,
    Bookmark,
    Coffee,
    Clock,
    Palette,
    UserCheck,
} from "lucide-vue-next";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
    roles: {
        type: Array,
        default: () => [],
    },
});

// Get role based on user ID
const getRole = (role_id) => {
    if (role_id === 1) {
        return "Admin";
    } else if (role_id === 2) {
        return "Staff";
    } else if (role_id === 3) {
        return "User";
    } else {
        return "Unknown";
    }
};

const getWelcomeMessage = (roleId) => {
    const messages = {
        1: "manage the Tribes platform", // Admin
        2: "create culture masterpieces", // Staff
        3: "explore tribes", // User
    };
    return messages[roleId] || "use the Tribe Book platform"; // Default message
};

const getRoleFeatures = (roleId) => {
    const features = {
        1: [
            {
                icon: Users,
                title: "User Management",
                description: "Oversee all platform users",
            },
            {
                icon: Book,
                title: "Tribe Oversight",
                description: "Curate and manage tribes",
            },
            {
                icon: Activity,
                title: "Platform Insights",
                description: "Monitor site activity",
            },
        ],
        2: [
            {
                icon: Palette,
                title: "Tribe Creation",
                description: "Craft your signature dishes",
            },
        ],
        3: [
            {
                icon: Search,
                title: "Tribe Discovery",
                description: "Find your next favorite meal",
            },
        ],
    };
    return features[roleId] || [];
};

const getDashboardRoute = (roleId) => {
    const routes = {
        1: route("admin"),
        2: route("staff"),
        3: route("user"), // Ensure this is the correct route
    };
    return routes[roleId] || route("dashboard");
};

const getDashboardIcon = (roleId) => {
    const icons = {
        1: ClipboardList,
        2: UserCheck,
        3: Book,
    };
    return icons[roleId] || Activity;
};

const getDashboardButtonText = (roleId) => {
    const texts = {
        1: "Go to Admin Dashboard",
        2: "Manage Your Tribes",
        3: "Explore Tribes",
    };
    return texts[roleId] || "Go to Dashboard";
};

const getQuickStats = (roleId) => {
    const stats = {
        1: [
            { title: "Total Users", value: "5,231", icon: Users },
            { title: "Total Staffs", value: "142", icon: UserCheck },
            { title: "Total Tribes", value: "1,205", icon: Book },
        ],
        2: [
            {
                title: "Your Tribes",
                value: "28",
                icon: Book,
                trendIcon: TrendingUp,
            },
            {
                title: "Total Views",
                value: "15.2K",
                icon: Eye,
                trendIcon: TrendingUp,
            },
        ],
    };
    return stats[roleId] || [];
};
</script>

<style scoped>
/* Add any additional component-specific styles here */
</style>
