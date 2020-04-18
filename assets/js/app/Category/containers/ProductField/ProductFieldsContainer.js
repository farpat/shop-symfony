import {connect} from "react-redux";
import ProductFieldsComponent from "../../components/ProductField/ProductFieldsComponent";

const mapStateToProps = (state) => {
    return {
        productFields: state.productFields.allProductFields
    }
};

const mapDispatchToProps = (dispatch) => {
    return {}
};

export default connect(mapStateToProps, mapDispatchToProps)(ProductFieldsComponent);