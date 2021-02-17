<template>
    <table class="table w-full">
        <thead>
        <TableHeadRow>
            <slot name="head"/>

            <!-- Actions -->
            <th>&nbsp;</th>
        </TableHeadRow>
        </thead>
        <tbody>
        <slot
            name="row"
            v-for="(item, i) in items"
            :item="item"
        >
            <TableRow :key="i">
                <slot :item="item"/>
                <td class="td-fit text-right pr-6 align-middle">
                    <slot :item="item" name="actions"/>
                </td>
            </TableRow>
        </slot>
        </tbody>
    </table>
</template>

<script>
import TableHeadRow from '../components/TableHeadRow';
import TableRow from '../components/TableRow';

export default {
    components: {
        TableHeadRow,
        TableRow,
    },
    props: {
        items: {
            type: Array,
            required: true,
        },
    },
}
</script>
