export default (state = {}, action) => {
    switch (action.type) {
        case 'UPDATE_ACTIVATED_SLIDER_INDEX_BY_REFERENCE':
            const activatedSliderIndexByReference = {
                ...state.activatedSliderIndexByReference,
                [action.reference.id]: action.index
            };


            return {
                ...state,
                activatedSliderIndexByReference
            };
        case 'UPDATE_REFERENCE':
            return {
                ...state,
                currentReference: action.reference
            };
        default:
            return state;
    }
}