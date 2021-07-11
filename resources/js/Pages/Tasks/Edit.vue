<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Task
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                    <form>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                Parent
                            </label>
                            <select v-model="form.parent_id" :class="{'border-red-500' : errors.parent_id}" class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                                <option value="null">None</option>
                                <option v-for="(val, key) in tasks" :value="key" :key="key">{{val}}</option>
                            </select>    
                            <p v-if="errors.parent_id" class="text-red-500 text-xs italic">{{errors.parent_id}}</p>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                Name
                            </label>
                            <input v-model="form.name" :class="{'border-red-500' : errors.name}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" placeholder="Name">
                            <p v-if="errors.name" class="text-red-500 text-xs italic">{{errors.name}}</p>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                Description
                            </label>
                            <textarea v-model="form.description" :class="{'border-red-500' : errors.description}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" cols="30" rows="10"></textarea>
                            <p v-if="errors.name" class="text-red-500 text-xs italic">{{errors.name}}</p>
                        </div>
                        <div class="flex items-center justify-end">
                            <inertia-link href="/tasks" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-block mr-3">
                                Cancel
                            </inertia-link>
                            <button @click="save(form)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import Welcome from '@/Jetstream/Welcome'

    export default {
        props: [
            'task',
            'tasks',
            'errors'
        ],
        components: {
            AppLayout,
            Welcome
        },
        data() {
            return {
                form: {
                    parent_id : this.task.parent_id,
                    name : this.task.name,
                    description : this.task.description
                }
            }
        },
        methods: {
            reset: function () {
                this.form = {
                    name : null,
                    description : null,
                    parent_id : null
                }
            },
            save: function (data) {
                this.$inertia.post('/tasks', data)
            }
        }
    }
</script>
