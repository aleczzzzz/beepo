
<template>
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
          <p>{{ element.name }} - {{element.status}}</p>
          <p>{{ element.description }}</p>
          <div class="btn-container py-3 px-4">
            <button @click="complete(element.id)" type="button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-3">Complete</button>
            <inertia-link :href="'/tasks/' + element.id + '/edit'" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-3">Edit</inertia-link>
            <button @click="del(element.id)" type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Delete</button>
          </div>
        </div>
        <nested-draggable :tasks="element.pending_children" @change="drop($event,element.id)" :drop="drop"/>
      </li>
    </template>
  </draggable>
</template>
<script>
  import draggable from "vuedraggable";

  export default {
    props: {
      tasks: {
        required: true,
        type: Array
      },
      drop: { 
        type: Function 
      },
    },
    components: {
      draggable
    },
    computed: {
      dragOptions() {
        return {
          animation: 200,
          group: "description",
          disabled: false,
          ghostClass: "ghost"
        };
      }
    },
    methods: {
      del: function (id) {
          this.$inertia.delete(`/tasks/${id}`)
      },
      complete: function (id) {
          this.$inertia.patch(`/tasks/${id}/update-status`)
      }
    },
    name: "nested-draggable"
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