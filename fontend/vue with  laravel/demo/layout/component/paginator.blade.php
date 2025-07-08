<script type="text/x-template" id="pagination">
    <div v-if="pagination.listPage.length > 3">
        <div class="text-muted mt-1 small" v-if="showLabel && pagination.meta">
            Showing @{{ pagination.meta.fromData }}–@{{ pagination.meta.toData }} of @{{ pagination.meta.total }} records
        </div>
        <div v-if="showPaginate">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <template v-for="(page, index) in pagination.listPage" :key="index">
                        <li class="page-item" :class="{ active: page.active }">
                            <a class="page-link" :href="page?.url ?? ''" @click.prevent="changePage(page?.url ?? null)">
                                <span v-if="page.label === '&laquo; Previous'">‹ Previous</span>
                                <span v-else-if="page.label === 'Next &raquo;'">Next ›</span>
                                <span v-else v-html="page.label"></span>
                            </a>
                        </li>
                    </template>
                </ul>
            </nav>
        </div>
    </div>
</script>

<script>
    const PaginationComponent = {
        props: ['pagination', 'showLabel', 'showPaginate'],
        template: '#pagination',
        methods: {
            changePage(page) {
                if (!page || page.active) return;
                this.$emit('change-page', page);
            }
        }
    };
    window.GlobalComponents = window.GlobalComponents || {};
    window.GlobalComponents.PaginationComponent = PaginationComponent;
</script>