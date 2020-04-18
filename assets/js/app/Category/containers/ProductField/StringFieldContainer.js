import {connect} from "react-redux";
import StringFieldComponent from "../../components/ProductField/StringFieldComponent";

const mapStateToProps = (state) => {
    return {
        filters: state.productFields.filters,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        updateFilter: (key, value) => {
            dispatch({type: 'UPDATE_FILTER', key, value});
        },
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(StringFieldComponent);