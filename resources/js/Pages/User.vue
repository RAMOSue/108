<template>
  <Head title="Browse Tribes" />
  <AuthenticatedLayout>
    <div class="flex h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
      <!-- Sidebar -->
      <aside class="w-64 bg-white shadow-xl">
        <div class="p-6 flex flex-col items-center">
          <img 
            :src="`https://ui-avatars.com/api/?name=${$page.props.auth.user.name}&background=random`" 
            alt="User Avatar" 
            class="w-24 h-24 rounded-full mb-4 shadow-lg"
            :class="avatarBorderColor"
          />
          <h2 class="text-xl font-bold text-gray-800">{{ $page.props.auth.user.name }}</h2>
          <p class="text-sm text-gray-600 mb-2">{{ $page.props.auth.user.email }}</p>
          <p :class="roleBadgeColor" class="px-3 py-1 rounded-full text-xs font-semibold">
            {{ userRole }}
          </p>
        </div>
      </aside>

      <!-- Main Content -->
      <main class="flex-1 overflow-x-hidden overflow-y-auto">
        <div class="container mx-auto px-6 py-8">
          <h1 class="text-4xl font-bold text-gray-800 mb-8">Philippine Cultural Groups</h1>
          
          <!-- Search and Filter -->
          <div class="mb-8 bg-white p-6 rounded-xl shadow-md">
            <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
              <div class="flex-grow">
                <input 
                  v-model="searchQuery" 
                  type="text" 
                  placeholder="Search tribes" 
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                  @input="searchTribess"
                >
              </div>
              <div class="md:w-48">
                <select 
                  v-model="selectedCategory"
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                  @change="filterTribes"
                >
                  <option value="">All Categories</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.category_name }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Tribe Grid -->
          <div v-if="filteredTribes.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <div v-for="tribe in filteredTribes" :key="tribe.id" 
                 class="bg-white overflow-hidden shadow-lg rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
              <img :src="tribe.url_image || '/placeholder.svg'" :alt="tribe.tribe_name" class="w-full h-48 object-cover">
              <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ tribe.tribe_name }}</h3>
                <p :style="{ color: getCategoryColor(tribe.category_id).text, backgroundColor: getCategoryColor(tribe.category_id).bg }" class="text-sm mb-4 font-medium inline-block px-2 py-1 rounded-full">
                  {{ getCategoryName(tribe.category_id) }}
                </p>
                <div class="flex items-center justify-between text-gray-500 mb-4">
                  <span class="flex items-center">
                    <ClockIcon class="w-4 h-4 mr-1" />
                    {{ tribe.population }}
                  </span>
                  <span class="flex items-center">
                    <UsersIcon class="w-4 h-4 mr-1" />
                    {{ tribe.region }} region
                  </span>
                </div>
                <button @click="viewTribeDetails(tribe)" 
                        class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-indigo-700 transform hover:-translate-y-1">
                  View more details
                </button>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-12 bg-white rounded-xl shadow-md">
            <BookOpenIcon class="h-24 w-24 text-indigo-400 mx-auto mb-4" />
            <p class="text-gray-500 text-lg">No tribes found. Try adjusting your search or filter.</p>
          </div>
        </div>
      </main>

      <!-- Tribes Details Modal -->
      <TransitionRoot appear :show="!!selectedTribe" as="template">
        <Dialog as="div" @close="closeTribeDetails" class="relative z-50">
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
            <div class="flex min-h-full items-center justify-center p-4 text-center">
              <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95" enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                <DialogPanel class="w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                  <div class="flex justify-between items-center mb-4">
                    <DialogTitle as="h2" class="text-3xl font-bold text-gray-800">
                      {{ selectedTribe?.tribe_name }}
                    </DialogTitle>
                    <button @click="closeTribeDetails" class="text-gray-500 hover:text-gray-700">
                      <XMarkIcon class="h-6 w-6" />
                    </button>
                  </div>
                  <img :src="selectedTribe?.url_image || '/placeholder.svg'" :alt="selectedTribe?.tribe_name" class="w-full h-64 object-cover rounded-xl mb-4">
                  <div class="flex justify-between items-center text-gray-600 mb-4">
                    <span class="flex items-center">
                      <ClockIcon class="w-5 h-5 mr-2" />
                      {{ selectedTribe?.population }}
                    </span>
                    <span class="flex items-center">
                      <UsersIcon class="w-5 h-5 mr-2" />
                      {{ selectedTribe?.region }} region
                    </span>
                    <span :style="{ color: getCategoryColor(selectedTribe?.category_id).text, backgroundColor: getCategoryColor(selectedTribe?.category_id).bg }" class="px-3 py-1 rounded-full text-sm font-medium">
                      {{ getCategoryName(selectedTribe?.category_id) }}
                    </span>
                  </div>
                  <div class="mb-4 p-4 bg-gray-100 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2">Staff</h3>
                    <div class="flex items-center">
                      <img 
                        :src="`https://ui-avatars.com/api/?name=${getUser(selectedTribe?.user_id)}&background=random`" 
                        :alt="getUser(selectedTribe?.user_id)"
                        class="w-10 h-10 rounded-full mr-3"
                      />
                      <div>
                        <p class="font-medium text-gray-800">{{ getUser(selectedTribe?.user_id) }}</p>
                        <p class="text-sm text-gray-600">Created on {{ formatDate(selectedTribe?.created_at) }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="space-y-4 max-h-[calc(100vh-24rem)] overflow-y-auto pr-4 custom-scrollbar">
                    <div>
                      <h3 class="text-xl font-semibold mb-2">Description</h3>
                      <p class="text-gray-700">{{ selectedTribe?.description }}</p>
                    </div>
                    <div>
                      <h3 class="text-xl font-semibold mb-2">Language</h3>
                      <ul class="list-disc list-inside text-gray-700">
                        <li v-for="language in selectedTribe?.language.split('\n')" :key="language" class="mb-2 break-words">
                          {{ language.trim() }}
                        </li>
                      </ul>
                    </div>
                    <div>
                      <h3 class="text-xl font-semibold mb-2">Culture</h3>
                      <ol class="list-decimal list-inside text-gray-700">
                        <li v-for="(step, index) in selectedTribe?.cultural_practices.split('\n')" :key="index" class="mb-2 break-words">
                          {{ step.trim() }}
                        </li>
                      </ol>
                    </div>
                  </div>
                </DialogPanel>
              </TransitionChild>
            </div>
          </div>
        </Dialog>
      </TransitionRoot>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { 
  ClockIcon, 
  UsersIcon, 
  BookOpenIcon,
  XMarkIcon,
  HomeIcon,
  UserIcon,
  CogIcon,
  HeartIcon,
  StarIcon
} from '@heroicons/vue/24/solid';

const page = usePage();

const props = defineProps({
  tribes: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  },
  roles: {
    type: Array,
    default: () => []
  },
  users: {
    type: Array,
    default: () => []
  },
});

const getUser = (user_id) => {
  const user = props.users.find(u => u.id === user_id);
  return user ? user.name : "Unknown";
};

// Reactive data
const roles = ref(props.roles);
const tribes = ref(props.tribes);
const categories = ref(props.categories);
const searchQuery = ref('');
const selectedCategory = ref('');
const selectedTribe = ref(null);

// Computed properties
const filteredTribes = computed(() => {
  return tribes.value.filter(tribe => {
    const matchesCategory = selectedCategory.value === '' || tribe.category_id === selectedCategory.value;
    const matchesSearch = tribe.tribe_name.toLowerCase().includes(searchQuery.value.toLowerCase());
    return matchesCategory && matchesSearch;
  });
});

const userRole = computed(() => {
  const roleId = page.props.auth.user.role_id;
  const role = props.roles.find(r => r.id === roleId);
  return role ? role.role_user : "Unknown";
});

const avatarBorderColor = computed(() => {
  switch (userRole.value) {
    case 'Admin':
      return 'border-4 border-green-500';
    case 'Staff':
      return 'border-4 border-blue-500';
    case 'User':
      return 'border-4 border-yellow-500';
    default:
      return 'border-4 border-gray-500';
  }
});

const roleBadgeColor = computed(() => {
  switch (userRole.value) {
    case 'Admin':
      return 'bg-green-100 text-green-800';
    case 'Staff':
      return 'bg-blue-100 text-blue-800';
    case 'User':
      return 'bg-yellow-100 text-yellow-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
});

// Methods
const getCategoryName = (categoryId) => {
  const category = categories.value.find(c => c.id === categoryId);
  return category ? category.category_name : 'Uncategorized';
};

const getCategoryColor = (categoryId) => {
  const category = categories.value.find(c => c.id === categoryId);
  if (category) {
    switch (category.category_name.toLowerCase()) {
      case 'main course':
        return { text: '#FFA500', bg: '#FFF3E0' }; // Orange
      case 'appetizer':
        return { text: '#FFD700', bg: '#FFFDE7' }; // Yellow
      case 'dessert':
        return { text: '#FF69B4', bg: '#FCE4EC' }; // Pink
      case 'beverage':
        return { text: '#00CED1', bg: '#E0F7FA' }; // Turquoise
      default:
        return { text: '#000000', bg: '#FFFFFF' };
    }
  }
  return { text: '#000000', bg: '#FFFFFF' };
};

const searchTribes = () => {
  // In a real application, you might want to debounce this function
  console.log('Searching:', searchQuery.value);
};

const filterTribes = () => {
  console.log('Filtering by category:', selectedCategory.value);
};

const viewTribeDetails = (tribe) => {
  selectedTribe.value = tribe;
};

const closeTribeDetails = () => {
  selectedTribe.value = null;
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

// Lifecycle hooks
onMounted(() => {
  // Fetch tribes if not provided as props
  if (tribes.value.length === 0) {
    fetchTribes();
  }
});

const fetchTribes = async () => {
  try {
    const response = await fetch(route('user.getTribes'));
    const data = await response.json();
    tribes.value = data.tribes;
    categories.value = data.categories;
  } catch (error) {
    console.error('Error fetching tribes:', error);
  }
};
</script>

<style scoped>
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #4F46E5 #E5E7EB;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #E5E7EB;
  border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #4F46E5;
  border-radius: 4px;
  border: 2px solid #E5E7EB;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #4338CA;
}
</style>