!function(e){var t={};function i(o){if(t[o])return t[o].exports;var c=t[o]={i:o,l:!1,exports:{}};return e[o].call(c.exports,c,c.exports,i),c.l=!0,c.exports}i.m=e,i.c=t,i.d=function(e,t,o){i.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},i.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.t=function(e,t){if(1&t&&(e=i(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(i.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var c in e)i.d(o,c,function(t){return e[t]}.bind(null,c));return o},i.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return i.d(t,"a",t),t},i.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},i.p="",i(i.s=1569)}({1569:function(e,t,i){"use strict";(function(e){i(641);var t=lc_data.product_listing,o=lc_data.payment_package,c=lc_data,n=c.promotion,a=c.commission,d=c.payment_subscription,s=document.getElementById("carbon_fields_container_product_information1"),r=document.getElementById("carbon_fields_container_package_information"),_=document.getElementById("carbon_fields_container_promotion_information"),l=document.getElementById("carbon_fields_container_payment_subscription_information"),u=document.getElementById("postexcerpt"),f=e("#product-type");function p(){var t=document.getElementsByName("carbon_fields_compact_input[_product-category]"),i=JSON.parse(lc_data.taxonomies);if(t&&"listing"===e("#product-type").val()){var o=t[0].value,c=["submit","postimage","woocommerce-product-images","carbon_fields_container_product_videos"];e("#side-sortables").children().each((function(t,n){var a=e(n).attr("id").replace("div","");e(n).hide(),i[o]?(i.common.includes(a)||i[o].includes(a)||c.includes(a))&&e(n).show():(i.common.includes(a)||c.includes(a))&&e(n).show()}))}else e("#side-sortables").children().each((function(t,i){var o=e(i).attr("id");e(i).hide(),["submitdiv","postimagediv","woocommerce-product-images","product_catdiv","tagsdiv-product_tag"].includes(o)&&e(i).show()}))}f.change((function(i){s.classList.add("is-hidden"),r.classList.add("is-hidden"),_.classList.add("is-hidden"),l.classList.add("is-hidden"),u.classList.remove("hide-if-js"),t===i.target.value&&s.classList.remove("is-hidden"),o===i.target.value&&r.classList.remove("is-hidden"),d===i.target.value&&l.classList.remove("is-hidden"),n===i.target.value&&_.classList.remove("is-hidden"),t!==i.target.value&&o!==i.target.value&&n!==i.target.value&&a!==i.target.value||u.classList.add("hide-if-js"),a===i.target.value?e("._sale_price_field, ._stock_custom_field").addClass("hide-if-js"):e("._sale_price_field, ._stock_custom_field").removeClass("hide-if-js"),n!==i.target.value&&o!==i.target.value&&d!==i.target.value?e("#inventory_product_data ._sold_individually_field input[type=checkbox]").prop("checked",!0):e("#inventory_product_data ._sold_individually_field input[type=checkbox]").prop("checked",!1)})),f.change(),e(".general_options").addClass("show_if_simple show_if_grouped show_if_variable show_if_".concat(t," show_if_").concat(o," show_if_").concat(n," show_if_").concat(a," show_if_").concat(d)),e(".options_group.pricing").addClass("show_if_".concat(t," show_if_").concat(o," show_if_").concat(n," show_if_").concat(a," show_if_").concat(d)).show(),e(".shipping_options").addClass("hide_if_".concat(t," hide_if_").concat(o," hide_if_").concat(n," hide_if_").concat(a," hide_if_").concat(d)),e(".linked_product_options").addClass("hide_if_".concat(t," hide_if_").concat(o," hide_if_").concat(n," hide_if_").concat(a," hide_if_").concat(d)),e(".attribute_options").addClass("hide_if_".concat(t," hide_if_").concat(o," hide_if_").concat(n," hide_if_").concat(a," hide_if_").concat(d)),e(".variations_options").addClass("hide_if_".concat(t," hide_if_").concat(o," hide_if_").concat(n," hide_if_").concat(a," hide_if_").concat(d)),e(".advanced_options").addClass("hide_if_".concat(t," hide_if_").concat(o," hide_if_").concat(n," hide_if_").concat(a," hide_if_").concat(d)),document.addEventListener("DOMContentLoaded",(function(){p(),e('select[name="carbon_fields_compact_input[_product-category]"]').on("change",(function(){p()})),e("#product-type").on("change",(function(){p()})),document.querySelectorAll(".click-to-copy").forEach((function(e){e.addEventListener("click",(function(){!function(e){var t=document.createElement("textarea");t.value=e,document.body.appendChild(t),t.select(),document.execCommand("copy"),document.body.removeChild(t)}(e.dataset.value);var t=document.createElement("span"),i=document.createTextNode("Copied!");t.appendChild(i),e.appendChild(t),setTimeout((function(){t.remove()}),1e3)}))}));var t=document.querySelector(".cf-datetime__input");t&&t.setAttribute("autocomplete","off")}))}).call(this,i(265))},265:function(e,t){e.exports=jQuery},641:function(e,t,i){}});