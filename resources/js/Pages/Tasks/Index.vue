<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tasks
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="btn-container flex justify-between py-3 px-4">
                    <inertia-link
                        href="/tasks/create"
                        class="
                            bg-blue-500
                            hover:bg-blue-700
                            text-white
                            font-bold
                            py-2
                            px-4
                            mb-3
                            mr-2
                            rounded
                            inline-block
                        "
                    >
                        Add New
                    </inertia-link>

                    <jet-dropdown align="right" width="48" class="inline-block">
                        <template #trigger>
                            <button
                                v-if="
                                    $page.props.jetstream.managesProfilePhotos
                                "
                                class="
                                    bg-blue-500
                                    hover:bg-blue-700
                                    text-white
                                    font-bold
                                    py-2
                                    px-4
                                    mb-3
                                    mr-3
                                    rounded
                                    focus:outline-none focus:shadow-outline
                                    inline-block
                                "
                            >
                                <img
                                    class="h-8 w-8 rounded-full object-cover"
                                    :src="$page.props.user.profile_photo_url"
                                    :alt="$page.props.user.name"
                                />
                            </button>

                            <span v-else class="inline-flex rounded-md">
                                <button
                                    type="button"
                                    class="
                                        inline-flex
                                        items-center
                                        px-4
                                        py-2
                                        border border-transparent
                                        leading-4
                                        font-bold
                                        rounded-md
                                        text-gray-500
                                        bg-white
                                        hover:text-gray-700
                                        focus:outline-none
                                        transition
                                    "
                                >
                                    Download

                                    <svg
                                        class="ml-2 -mr-0.5 h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </span>
                        </template>

                        <template #content>
                            <a
                                :href="route('tasks.download.xls')"
                                class="
                                    block
                                    px-4
                                    py-2
                                    text-sm
                                    leading-5
                                    text-gray-700
                                    hover:bg-gray-100
                                    focus:outline-none focus:bg-gray-100
                                    transition
                                "
                            >
                                Excel
                            </a>

                            <a
                                :href="route('tasks.download.csv')"
                                class="
                                    block
                                    px-4
                                    py-2
                                    text-sm
                                    leading-5
                                    text-gray-700
                                    hover:bg-gray-100
                                    focus:outline-none focus:bg-gray-100
                                    transition
                                "
                            >
                                CSV
                            </a>
                        </template>
                    </jet-dropdown>
                </div>
                <div
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8"
                >
                    <nested-draggable
                        :tasks="tasks"
                        @change="drop($event, null)"
                        :drop="drop"
                    />
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Welcome from "@/Jetstream/Welcome";
import nestedDraggable from "./partials/nested";
import JetDropdown from "@/Jetstream/Dropdown";
import JetDropdownLink from "@/Jetstream/DropdownLink";

export default {
    props: ["tasks"],
    components: {
        AppLayout,
        Welcome,
        JetDropdown,
        JetDropdownLink,
        nestedDraggable,
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
    },
};
</script>
