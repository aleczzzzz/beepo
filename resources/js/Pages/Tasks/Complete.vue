<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tasks
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8"
                >
                    <draggable
                        class="dragArea"
                        tag="ul"
                        :list="tasks"
                        :group="{ name: 'g1' }"
                        item-key="name"
                        v-bind="dragOptions"
                        @start="drag = true"
                        @end="drag = false"
                    >
                        <template #item="{ element }">
                            <li>
                                <div class="flex items-center justify-between">
                                    <p>
                                        {{ element.name }} -
                                        {{ element.status }}
                                    </p>
                                    <div class="btn-container py-3 px-4">
                                        <button
                                            @click="complete(element.id)"
                                            type="button"
                                            class="
                                                bg-green-500
                                                hover:bg-green-700
                                                text-white
                                                font-bold
                                                py-2
                                                px-4
                                                rounded
                                                focus:outline-none
                                                focus:shadow-outline
                                                mr-3
                                            "
                                        >
                                            Pending
                                        </button>
                                        <button
                                            @click="del(element.id)"
                                            type="button"
                                            class="
                                                bg-red-500
                                                hover:bg-red-700
                                                text-white
                                                font-bold
                                                py-2
                                                px-4
                                                rounded
                                                focus:outline-none
                                                focus:shadow-outline
                                            "
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </li>
                        </template>
                    </draggable>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Welcome from "@/Jetstream/Welcome";
import draggable from "vuedraggable";

export default {
    props: ["tasks"],
    components: {
        AppLayout,
        Welcome,
        draggable,
    },
    data() {
        return {
            drag: false,
        };
    },
    computed: {
        dragOptions() {
            return {
                animation: 200,
                group: "description",
                disabled: false,
                ghostClass: "ghost",
            };
        },
    },
    methods: {
        drop: function (evt, parentId) {
            if (evt?.added) {
                let event = evt.added;
                let element = event.element;
                let index = event.newIndex;

                this.$inertia.post(`/tasks/${element.id}/update-position`, {
                    parent_id: parentId,
                    index: index,
                });
            }
            if (evt?.moved) {
                let event = evt.moved;
                let element = event.element;
                let index = event.newIndex;

                this.$inertia.post(`/tasks/${element.id}/update-position`, {
                    parent_id: parentId,
                    index: index,
                });
            }
        },
        del: function (id) {
          this.$inertia.delete(`/tasks/${id}`)
        },
        complete: function (id) {
            this.$inertia.patch(`/tasks/${id}/update-status`)
        }
    },
};
</script>
<style scoped>
  .dragArea {
    min-height: 50px;
    outline: 1px dashed;
    padding-left: 40px;
    padding-bottom: 20px;
  }
</style>
