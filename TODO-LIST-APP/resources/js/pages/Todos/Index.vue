<template>
  <AppLayout title="Todo List">
    <template #header>
      <Heading title="My Todo List" />
    </template>

    <div class="space-y-6">
      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <Card class="p-4">
          <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">{{ stats.total }}</div>
            <div class="text-sm text-gray-600">Total Tasks</div>
          </div>
        </Card>
        <Card class="p-4">
          <div class="text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</div>
            <div class="text-sm text-gray-600">Pending</div>
          </div>
        </Card>
        <Card class="p-4">
          <div class="text-center">
            <div class="text-2xl font-bold text-purple-600">{{ stats.in_progress }}</div>
            <div class="text-sm text-gray-600">In Progress</div>
          </div>
        </Card>
        <Card class="p-4">
          <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ stats.completed }}</div>
            <div class="text-sm text-gray-600">Completed</div>
          </div>
        </Card>
        <Card class="p-4">
          <div class="text-center">
            <div class="text-2xl font-bold text-red-600">{{ stats.overdue }}</div>
            <div class="text-sm text-gray-600">Overdue</div>
          </div>
        </Card>
      </div>

      <!-- New todo -->
      <Card class="p-4">
        <form @submit.prevent="createTodo" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label for="title">Title</Label>
              <Input
                id="title"
                v-model="newTodo.title"
                placeholder="What needs to be done?"
                required
              />
            </div>
            <div>
              <Label for="priority">Priority</Label>
              <select
                id="priority"
                v-model="newTodo.priority"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
              </select>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label for="description">Description</Label>
              <textarea
                id="description"
                v-model="newTodo.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Add details..."
              ></textarea>
            </div>
            <div>
              <Label for="due_date">Due Date</Label>
              <Input
                id="due_date"
                v-model="newTodo.due_date"
                type="date"
                class="w-full"
              />
            </div>
          </div>
          <div class="flex justify-end">
            <Button type="submit" :disabled="isSubmitting">
              {{ isSubmitting ? 'Creating...' : 'Add Todo' }}
            </Button>
          </div>
        </form>
      </Card>

      <!-- Todos -->
      <div class="space-y-3">
        <div
          v-for="todo in todos.data"
          :key="todo.id"
          class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <button
                  @click="toggleStatus(todo)"
                  class="flex-shrink-0"
                >
                  <div
                    :class="[
                      'w-5 h-5 rounded-full border-2 flex items-center justify-center',
                      todo.status === 'completed'
                        ? 'bg-green-500 border-green-500'
                        : 'border-gray-300'
                    ]"
                  >
                    <svg
                      v-if="todo.status === 'completed'"
                      class="w-3 h-3 text-white"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </div>
                </button>
                <h3
                  :class="[
                    'text-lg font-medium',
                    todo.status === 'completed' ? 'line-through text-gray-500' : 'text-gray-900'
                  ]"
                >
                  {{ todo.title }}
                </h3>
                <span
                  :class="[
                    'px-2 py-1 text-xs font-medium rounded-full',
                    priorityClasses[todo.priority as keyof typeof priorityClasses]
                  ]"
                >
                  {{ todo.priority }}
                </span>
                <span
                  :class="[
                    'px-2 py-1 text-xs font-medium rounded-full',
                    statusClasses[todo.status as keyof typeof statusClasses]
                  ]"
                >
                  {{ todo.status.replace('_', ' ') }}
                </span>
              </div>
              <p
                v-if="todo.description"
                :class="[
                  'text-gray-600 mb-2',
                  todo.status === 'completed' ? 'line-through' : ''
                ]"
              >
                {{ todo.description }}
              </p>
              <div class="flex items-center gap-4 text-sm text-gray-500">
                <span v-if="todo.due_date">
                  Due: {{ formatDate(todo.due_date) }}
                  <span
                    v-if="isOverdue(todo.due_date) && todo.status !== 'completed'"
                    class="text-red-500 font-medium"
                  >
                    (Overdue)
                  </span>
                </span>
                <span>Created: {{ formatDate(todo.created_at) }}</span>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <Button
                variant="outline"
                size="sm"
                @click="editTodo(todo)"
              >
                Edit
              </Button>
              <Button
                variant="outline"
                size="sm"
                @click="deleteTodo(todo)"
                class="text-red-600 hover:text-red-700"
              >
                Delete
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>

  <!-- Edit Todo Dialog -->
  <Dialog v-model:open="isEditOpen">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Edit Todo</DialogTitle>
      </DialogHeader>

      <div class="space-y-4">
        <div>
          <Label for="edit_title">Title</Label>
          <Input id="edit_title" v-model="editForm.title" required />
        </div>
        <div>
          <Label for="edit_description">Description</Label>
          <textarea
            id="edit_description"
            v-model="editForm.description"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Add details..."
          ></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <Label for="edit_status">Status</Label>
            <select
              id="edit_status"
              v-model="editForm.status"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            >
              <option value="pending">Pending</option>
              <option value="in_progress">In Progress</option>
              <option value="completed">Completed</option>
            </select>
          </div>
          <div>
            <Label for="edit_priority">Priority</Label>
            <select
              id="edit_priority"
              v-model="editForm.priority"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            >
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
          </div>
          <div>
            <Label for="edit_due_date">Due Date</Label>
            <Input id="edit_due_date" v-model="editForm.due_date" type="date" class="w-full" />
          </div>
        </div>
      </div>

      <DialogFooter class="mt-4">
        <Button variant="outline" @click="isEditOpen = false">Cancel</Button>
        <Button @click="updateTodo">Save</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/app/AppSidebarLayout.vue'
import Heading from '@/components/Heading.vue'
import Card from '@/components/ui/card/Card.vue'
import Button from '@/components/ui/button/Button.vue'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog'

// Types
interface Todo {
  id: number
  title: string
  description?: string
  status: 'pending' | 'in_progress' | 'completed'
  priority: 'low' | 'medium' | 'high'
  due_date?: string
  created_at: string
  updated_at: string
  user_id: number
}

interface TodoStats {
  total: number
  pending: number
  in_progress: number
  completed: number
  overdue: number
}

interface TodoFilters {
  status?: string
  priority?: string
  search?: string
}

// Props
const props = defineProps<{
  todos: {
    data: Todo[]
    links?: any[]
  }
  filters: TodoFilters
  stats: TodoStats
}>()

// Reactive data
const newTodo = reactive({
  title: '',
  description: '',
  priority: 'medium' as const,
  due_date: ''
})

const isSubmitting = ref(false)
const isEditOpen = ref(false)
const editingTodo = ref<Todo | null>(null)
const editForm = reactive({
  title: '',
  description: '',
  status: 'pending' as Todo['status'],
  priority: 'medium' as Todo['priority'],
  due_date: '' as string | undefined
})

// Computed
const priorityClasses = {
  low: 'bg-gray-100 text-gray-800',
  medium: 'bg-yellow-100 text-yellow-800',
  high: 'bg-red-100 text-red-800'
} as const

const statusClasses = {
  pending: 'bg-blue-100 text-blue-800',
  in_progress: 'bg-purple-100 text-purple-800',
  completed: 'bg-green-100 text-green-800'
} as const

// Methods
const createTodo = async () => {
  isSubmitting.value = true
  try {
    await router.post('/todos', newTodo)
    // Reset form
    Object.assign(newTodo, {
      title: '',
      description: '',
      priority: 'medium' as const,
      due_date: ''
    })
  } finally {
    isSubmitting.value = false
  }
}

const editTodo = (todo: Todo) => {
  editingTodo.value = todo
  Object.assign(editForm, {
    title: todo.title,
    description: todo.description ?? '',
    status: todo.status,
    priority: todo.priority,
    due_date: todo.due_date ?? ''
  })
  isEditOpen.value = true
}

const deleteTodo = async (todo: Todo) => {
  if (confirm('Are you sure you want to delete this todo?')) {
    await router.delete(`/todos/${todo.id}`)
  }
}

const toggleStatus = async (todo: Todo) => {
  await router.patch(`/todos/${todo.id}/toggle-status`)
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString()
}

const isOverdue = (dateString: string) => {
  return new Date(dateString) < new Date()
}

const updateTodo = async () => {
  if (!editingTodo.value) return
  const payload = {
    title: editForm.title,
    description: editForm.description || undefined,
    status: editForm.status,
    priority: editForm.priority,
    due_date: editForm.due_date && editForm.due_date !== '' ? editForm.due_date : undefined,
  }
  await router.put(`/todos/${editingTodo.value.id}`, payload, {
    preserveScroll: true,
    onSuccess: () => {
      isEditOpen.value = false
      editingTodo.value = null
    }
  })
}
</script>
