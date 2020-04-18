export default (state = {}, action) => {
    switch (action.type) {
        case 'UPDATE_FILTER':

            return Object.assign({}, state, {filters: {...state.filters, [action.key]: action.value}});
        default:
            return state;
    }
}