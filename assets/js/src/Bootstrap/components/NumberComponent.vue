<template>
    <div>
        <label :for="getId" class="col-form-label" v-html="label" v-if="label"></label>

        <div :class="getContainerClass" class="input-group input-group-quantity">
            <button :class="getMinusButtonClass" :style="getMinusButtonStyle" @click="decrease($event)" type="button">
                <i class="fas fa-minus"></i>
            </button>

            <input :name="getName" :value="getValue" type="hidden">

            <span :class="getInputClass">{{ getFormattedValue }}</span>

            <button :class="getPlusButtonClass" :style="getPlusButtonStyle" @click="increase($event)" type="button">
                <i class="fas fa-plus"></i>
            </button>
        </div>

        <error-component :error="getError"></error-component>
    </div>
</template>

<script>
    import ErrorComponent from "../includes/ErrorComponent";
    import Str from "../../String/Str";
    import FormElementMixin from "../includes/FormElementMixin";

    export default {
        components: {ErrorComponent},
        mixins:     [FormElementMixin],
        mounted:    function () {
            if (this.getValue === undefined) {
                this.change(this.min);
            }
        },
        props:      {
            direction: {type: String, default: 'horizontal'},
            step:      {type: Number, default: 1},
            min:       {type: Number, default: 0},
            max:       {type: Number, default: -1},
        },
        computed:   {
            getQuantity:         function () {
                return this.getValue || 0;
            },
            getContainerClass:   function () {
                return 'input-group-quantity-' + this.direction;
            },
            getMinusButtonStyle: function () {
                return {
                    'cursor': this.canDecrease ? 'pointer' : 'initial'
                };
            },
            getPlusButtonStyle:  function () {
                return {
                    'cursor': this.canIncrease ? 'pointer' : 'initial'
                };
            },
            getMinusButtonClass: function () {
                return {
                    'invisible': !this.canDecrease
                };
            },
            getPlusButtonClass:  function () {
                return {
                    'invisible': !this.canIncrease
                };
            },
            getInputClass:       function () {
                return this.getError ? ' is-invalid' : '';
            },
            canIncrease:         function () {
                if (this.max === -1) return true;

                return (this.getQuantity + this.step <= this.max);
            },
            canDecrease:         function () {
                return (this.getQuantity - this.step >= this.min);
            },
            getFormattedValue:   function () {
                return Str.toLocaleNumber(this.getQuantity);
            },
        },
        methods:    {
            increase: function (event) {
                if (this.canIncrease) {
                    this.change(this.getQuantity + this.step);
                    this.$emit('increase', {quantity: this.getQuantity});
                }
            },

            decrease: function (event) {
                if (this.canDecrease) {
                    this.change(this.getQuantity - this.step);
                    this.$emit('decrease', {quantity: this.getQuantity});
                }
            },
        }
    }
</script>
