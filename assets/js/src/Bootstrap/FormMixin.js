import Store from "./Store";

export default {
    data:    function () {
        return {
            sharedState: Store.state
        }
    },
    mounted: function () {
        this.$store = Store;
    }
};
