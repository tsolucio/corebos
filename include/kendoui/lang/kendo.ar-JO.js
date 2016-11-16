/*
* Kendo UI Localization Project for v2012.3.1114 
* Copyright 2012 Telerik AD. All rights reserved.
* 
* Arabic JO (ar-JO) Language Pack
*
* Project home  : https://github.com/loudenvier/kendo-global
* Kendo UI home : http://kendoui.com
* Author        : Felipe Machado (Loudenvier) 
*                 http://feliperochamachado.com.br/index_en.html
*
* This project is released to the public domain, although one must abide to the 
* licensing terms set forth by Telerik to use Kendo UI, as shown bellow.
*
* Telerik's original licensing terms:
* -----------------------------------
* Kendo UI Web commercial licenses may be obtained at
* https://www.kendoui.com/purchase/license-agreement/kendo-ui-web-commercial.aspx
* If you do not own a commercial license, this file shall be governed by the
* GNU General Public License (GPL) version 3.
* For GPL requirements, please review: http://www.gnu.org/copyleft/gpl.html
*/
kendo.culture("ar-JO");
kendo.ui.Locale = "Arabic JO (ar-JO)";
kendo.ui.ColumnMenu.prototype.options.messages = 
  $.extend(kendo.ui.ColumnMenu.prototype.options.messages, {

/* COLUMN MENU MESSAGES 
 ****************************************************************************/   
      sortAscending: "فرز تصاعدي",
      sortDescending: "فرز تنازلي",
  filter: "مُرَشِّح",
  columns: "الأعمدة"
 /***************************************************************************/   
});

kendo.ui.Groupable.prototype.options.messages = 
  $.extend(kendo.ui.Groupable.prototype.options.messages, {

/* GRID GROUP PANEL MESSAGES 
 ****************************************************************************/   
      empty: "اسحب عنوان العمود وأسقطه هنا لعمل مجموعات حسب هذا العمود"
 /***************************************************************************/   
});

kendo.ui.FilterMenu.prototype.options.messages = 
  $.extend(kendo.ui.FilterMenu.prototype.options.messages, {
  
/* FILTER MENU MESSAGES 
 ***************************************************************************/   
      info: "اعرض المدخلات بحيث تكون القيمة:", // sets the text on top of the filter menu
  isTrue: "صحيحة",                   // sets the text for "isTrue" radio button
  isFalse: "غير صحيحة",                 // sets the text for "isFalse" radio button
  filter: "صَفَّي",                    // sets the text for the "Filter" button
  clear: "الغاء",                      // sets the text for the "Clear" button
  and: "و",
  or: "أو",
  selectValue: "-إختر قيمة-"
 /***************************************************************************/   
});
         
kendo.ui.FilterMenu.prototype.options.operators =           
  $.extend(kendo.ui.FilterMenu.prototype.options.operators, {

/* FILTER MENU OPERATORS (for each supported data type) 
 ****************************************************************************/   
  string: {
      eq: "تساوي لـ",
      neq: "لا تساوي لـ",
      startswith: "تبدأ بـ",
      contains: "تحتوي",
      doesnotcontain: "لا تحتوي",
      endswith: "تنتهي بـ"
  },
  number: {
      eq: "تساوي لـ",
      neq: "لا تساوي لـ",
      gte: "أكبر من أو تساوي لـ",
      gt: "أكبر من",
      lte: "أصغر من أو تساوي لـ",
      lt: "أصغر من"
  },
  date: {
      eq: "تساوي لـ",
      neq: "لا تساوي لـ",
      gte: "بعد او تساوي لـ",
      gt: "بعد",
      lte: "قبل او تساوي لـ",
      lt: "قبل"
  },
  enums: {
      eq: "تساوي لـ",
      neq: "لا تساوي لـ",
  }
 /***************************************************************************/   
});

kendo.ui.Pager.prototype.options.messages = 
  $.extend(kendo.ui.Pager.prototype.options.messages, {
  
/* PAGER MESSAGES 
 ****************************************************************************/   
display: "إظهار {0} إلى {1} من أصل {2} مُدخل",
empty: "لم يُعثر على أية مُدخلات",
page: "صفحة",
of: "من {0}",
itemsPerPage: "مدخل للصفحة",
first: "الأول",
previous: "السابق",
next: "التالي",
last: "الأخير",
refresh: "تحديث"
 /***************************************************************************/   
});

kendo.ui.Validator.prototype.options.messages = 
  $.extend(kendo.ui.Validator.prototype.options.messages, {

/* VALIDATOR MESSAGES 
 ****************************************************************************/   
  required: "{0} حقل الزامي",
  pattern: "{0} غير صحيح ",
  min: "{0} يجب أن يكون أكبر او يساوي {1}",
  max: "{0} يجب أن يكون أصغر او يساوي {1}",
  step: "{0} غير صحيح",
  email: "{0} ليس بريد إلكتروني صحيح",
  url: "{0} ليس رابط صحيح",
  date: "{0} ليس تاريخ صالح"
 /***************************************************************************/   
});

kendo.ui.ImageBrowser.prototype.options.messages = 
  $.extend(kendo.ui.ImageBrowser.prototype.options.messages, {

 /* IMAGE BROWSER MESSAGES 
 ****************************************************************************/   
  uploadFile: "رفع",
  orderBy: "ترتيب حسب",
  orderByName: "ترتيب حسب الإسم",
  orderBySize: "ترتيب حسب الحجم",
  directoryNotFound: "لم يتم ايجاد مجلد بهذا الإسم.",
  emptyFolder: "مجلد فارغ",
  deleteFile: 'هل أنت متأكد من حذف الملف "{0}"؟',
  invalidFileType: "الملف المحدد \"{0}\" غير صالح. أنواع الملفات المعتمدة هي \"{1}\".",
  overwriteFile: "يوجد ملف  بنفس الإسم \"{0}\" في المجلد الحالي. هل تريد استبداله؟",
  dropFilesHere: "اسحب الملفات وأسقطها هنا ليتم رفعها"
 /***************************************************************************/   
});

kendo.ui.Editor.prototype.options.messages = 
  $.extend(kendo.ui.Editor.prototype.options.messages, {

/* EDITOR MESSAGES 
 ****************************************************************************/   
  bold: "عريض",
  italic: "مائل",
  underline: "تسطير ",
  strikethrough: "يتوسطه خط",
  superscript: "مرتفع",
  subscript: "منخفض",
  justifyCenter: "توسيط",
  justifyLeft: "محاذاة إلى اليسار",
  justifyRight: "محاذاة إلى اليمين",
  justifyFull: "ملأ السطر",
  insertUnorderedList: "إدراج قائمة منقطة",
  insertOrderedList: "إدراج قائمة مرقمة",
  indent: "زيادة المسافة البادئة",
  outdent: "إنقاص المسافة البادئة",
  createLink: "إدراج رابط",
  unlink: "ازالة الرابط",
  insertImage: "إدراج صورة",
  insertHtml: "إدراج HTML",
  fontName: "نوع الخط",
  fontNameInherit: "(نوع الخط موروث)",
  fontSize: "حجم الخط",
  fontSizeInherit: "(حجم الخط موروث)",
  formatBlock: "تنسيق",
  foreColor: "لون الخط",
  backColor: "لون تظليل النص",
  style: "التنسيقات",
  emptyFolder: "مجلد فارغ",
  uploadFile: "ارفع ملف",
  orderBy: "ترتيب حسب:",
  orderBySize: "ترتيب حسب الحجم",
  orderByName: "ترتيب حسب الإسم",
  invalidFileType: "الملف المحدد \"{0}\" غير صالح. أنواع الملفات المعتمدة هي \"{1}\".",
  deleteFile: 'هل أنت متأكد من حذف هذا الملف "{0}"؟',
  overwriteFile: 'يوجد ملف  بنفس الإسم \"{0}\" في المجلد الحالي. هل تريد استبداله؟',
  directoryNotFound: "لم يتم ايجاد ملف بهذا الإسم.",
  imageWebAddress: "عنوان ويب",
  imageAltText: "نص بديل",
  dialogInsert: "إدراج",
  dialogButtonSeparator: "أو",
  dialogCancel: "الغاء"
 /***************************************************************************/   
});
