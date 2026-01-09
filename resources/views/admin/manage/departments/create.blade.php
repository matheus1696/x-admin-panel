<x-app-layout>

    <x-page.header
        icon="fa-solid fa-plus"
        title="New Department"
        subtitle="Create a new organizational unit"
    />

    <form method="POST" action="{{ route('config.departments.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf

        <div>
            <x-form.label value="Department Name" />
            <x-form.input name="title" required />
        </div>

        <div>
            <x-form.label value="Acronym" />
            <x-form.input name="acronym" />
        </div>

        <div class="md:col-span-2">
            <x-form.label value="Parent Department" />
            <x-form.select-search name="parent_id" :collection="$parents" labelField="title" valueField="id" default="Selecione o parente" :selected="old('parent_id', $department->parent_id ?? '')"/>
        </div>

        <div class="md:col-span-2">
            <x-form.label value="Description" />
            <textarea name="description" rows="3"></textarea>
        </div>

        <div class="md:col-span-2 flex justify-end gap-2 mt-4">
            <x-button.btn-submit value="Criar Usuário"/>
        </div>
    </form>

</x-app-layout>
