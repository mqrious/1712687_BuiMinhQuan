$(function() {
   const MAKE_ORDER_URL = "order.php?action=createOrder";
   const ORDER_SUCCESS_URL = "order.php?action=success";
   const HCMC_PROVINCE_CODE = 79;
   const PROVINCE_API = function (provinceCode = null) {
      return (provinceCode == null)
          ? "https://provinces.open-api.vn/api/"
          : `https://provinces.open-api.vn/api/p/${provinceCode}`
   }
   const DISTRICT_API = function (provinceCode) {
      return `https://provinces.open-api.vn/api/p/${provinceCode}/?depth=2`;
   };
   const WARD_API = function (districtCode) {
      return `https://provinces.open-api.vn/api/d/${districtCode}/?depth=2`;
   };

   let $customerNameInput = $("input#customerName");
   let $phoneNumberInput = $("input#phoneNumber");
   let $addressInput = $("input#address");
   let $wardSelect = $("select#ward");
   let $districtSelect = $("select#district");
   let $provinceSelect = $("select#province");
   let $serviceSelect = $("select#service");
   let $unitInput = $("input#unit");
   let $noteInput = $("input#note");

   // UI functions
   const displayTotalPrice = function (totalPrice = 0) {
      $("span.totalPrice").text(Number(totalPrice || 0).toLocaleString("de-DE"));
   }

   // Province Selects functions
   function displayProvinces(response) {
      let $provinceSelect = $("select#province");
      let provinceOptions = "";
      response.forEach(function (province, index) {
         provinceOptions += `<option value="${province.code}">${province.name}</option>`;
      });
      $provinceSelect.append(provinceOptions);
   }

   function displayDistricts(response) {
      let $districtSelect = $("select#district");
      let districtOptions = "";
      if (response["districts"] == []) {
         return;
      }
      response["districts"].forEach(function (district, index) {
         districtOptions += `<option value="${district.code}">${district.name}</option>`;
      });
      $districtSelect.append(districtOptions);
   }

   function displayWards(response) {
      let $wardSelect = $("select#ward");
      let wardOptions = "";
      if (response["wards"] == []) {
         return;
      }
      response["wards"].forEach(function (ward, index) {
         wardOptions += `<option value="${ward.code}">${ward.name}</option>`;
      });
      $wardSelect.append(wardOptions);
   }

   function resetDistricts() {
      let $districtSelect = $("select#district");
      $districtSelect.html("<option value=''>Quận/Huyện</option>");
      resetWards();
   }

   function resetWards() {
      let $wardSelect = $("select#ward");
      $wardSelect.html("<option value=''>Phường/Xã</option>");
   }

   // Data functions
   const calculateTotalPrice = function () {
      let servicePrice = ($serviceSelect.find("option:selected").first().val() || '') === ''
          ? 0
          : $serviceSelect.find("option:selected").first().data('price');
      let unit = $unitInput.val() || 0;
      let totalPrice = Number(servicePrice) * Number(unit);
      displayTotalPrice(totalPrice);
      return totalPrice;
   }

   const makeShippingAddress = function (address) {
      return address.join(", ");
   }

   const validate = function (success) {
      clearErrors();
      let addressLine = $addressInput.val() || '';
      let ward = ($wardSelect.find("option:selected").first().val() || '') === ''
          ? ''
          : $wardSelect.find("option:selected").first().text();
      let district = ($districtSelect.find("option:selected").first().val() || '') === ''
          ? ''
          : $districtSelect.find("option:selected").first().text();
      let province = ($provinceSelect.find("option:selected").first().val() || '') === ''
          ? ''
          : $provinceSelect.find("option:selected").first().text();
      let customerName = $customerNameInput.val() || '';
      let phoneNumber = $phoneNumberInput.val() || '';
      let service = ($serviceSelect.find("option:selected").first().val() || '') === ''
          ? ''
          : $serviceSelect.find("option:selected").first().val();
      let unit = $unitInput.val() || '';
      let note = $noteInput.val() || '';
      let passed = true;
      if (service === '') {
         displayError($serviceSelect, "Vui lòng chọn dịch vụ sửa chữa");
         passed = false;
      }
      if (customerName === '') {
         displayError($customerNameInput, "Vui lòng nhập tên đăng ký");
         passed = false;
      }
      if (phoneNumber === '') {
         displayError($phoneNumberInput, "Vui lòng nhập số điện thoại đăng ký");
         passed = false;
      }
      if (unit === '') {
         displayError($unitInput, "Vui lòng nhập số lượng dịch vụ");
         passed = false;
      }
      if (addressLine === '') {
         displayError($addressInput, "Vui lòng nhập địa chỉ");
         passed = false;
      }
      if (ward === '') {
         displayError($wardSelect, "Vui lòng chọn phường/xã");
         passed = false;
      }
      if (district === '') {
         displayError($districtSelect, "Vui lòng chọn quận/huyện");
         passed = false;
      }
      if (province === '') {
         displayError($provinceSelect, "Vui lòng chọn tỉnh/thành");
         passed = false;
      }
      if (passed) {
         let totalPrice = calculateTotalPrice();
         let data = {
            customerName: customerName,
            phoneNumber: phoneNumber,
            address: makeShippingAddress([addressLine, ward, district, province]),
            serviceId: service,
            unit: unit,
            note: note,
            totalPrice: totalPrice
         }
         success(data);
      }
   }

   // Event functions
   $("body").on("click", "select#province", function (e) {
      if ($(this).find("option").length < 2) {
         $.ajax({
            url: PROVINCE_API(HCMC_PROVINCE_CODE),
            method: "get",
            success: function (response) {
               displayProvinces([response]);
            },
            error: function (errors) {
               console.log(errors);
            },
         });
      }
   });

   $("body").on("change", "select#province", function (e) {
      let provinceCode = $(this).find("option:selected").first().val();
      if (provinceCode == "") return;
      resetDistricts();
      if ($("select#district option").length < 2) {
         $.ajax({
            url: DISTRICT_API(provinceCode),
            method: "get",
            success: function (response) {
               displayDistricts(response);
            },
            error: function (errors) {
               console.log(errors);
            },
         });
      }
   });

   $("body").on("change", "select#district", function (e) {
      let districtCode = $(this).find("option:selected").first().val();
      if (districtCode == "") return;
      resetWards();
      if ($("select#ward option").length < 2) {
         $.ajax({
            url: WARD_API(districtCode),
            method: "get",
            success: function (response) {
               displayWards(response);
            },
            error: function (errors) {
               console.log(errors);
            },
         });
      }
   });

   $("body").on("click", "button#makeOrder", function(e) {
      validate(
          (data) => {
             console.log(data);
             $.ajax({
                url: MAKE_ORDER_URL,
                method: "post",
                data: data,
                success: (response) => {
                   console.log(response);
                   alert(response.message);
                   window.location = ORDER_SUCCESS_URL;
                },
                error: (errors) => {
                   console.log(errors);
                   alert(errors.responseText);
                }
             });
          }
      )
   })

   $("body").on("change", "select#service", function(e) {
      calculateTotalPrice();
   })

   $("body").on("change", "input#unit", function(e) {
      calculateTotalPrice();
   })
});