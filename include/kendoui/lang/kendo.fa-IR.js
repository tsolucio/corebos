/*
* Kendo UI Localization Project for v2012.3.1114
* Copyright 2012 Telerik AD. All rights reserved.
*
* Persian (fa-IR) Language Pack
*
* Project home : https://github.com/loudenvier/kendo-global
* Kendo UI home : http://kendoui.com
* Author : Bahman Nikkhahan
*
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
kendo.ui.Locale = "Persian (fa-IR)";
kendo.ui.ColumnMenu.prototype.options.messages =
  $.extend(kendo.ui.ColumnMenu.prototype.options.messages, {

      /* COLUMN MENU MESSAGES 
       ****************************************************************************/
      sortAscending: "مرتب سازی صعودی",
      sortDescending: "مرتب سازی نزولی",
      filter: "فیلتر",
      columns: "ستون ها"
      /***************************************************************************/
  });

kendo.ui.Groupable.prototype.options.messages =
  $.extend(kendo.ui.Groupable.prototype.options.messages, {

      /* GRID GROUP PANEL MESSAGES 
       ****************************************************************************/
      empty: "ستون ها را جهت گروه بندی در اینجا قرار دهید"
      /***************************************************************************/
  });

kendo.ui.FilterMenu.prototype.options.messages =
  $.extend(kendo.ui.FilterMenu.prototype.options.messages, {

      /* FILTER MENU MESSAGES 
       ***************************************************************************/
      info: "نشان دادن مواردی که:",        // sets the text on top of the filter menu
      filter: "فیلتر",      // sets the text for the "Filter" button
      clear: "پاک کردن",        // sets the text for the "Clear" button
      // when filtering boolean numbers
      isTrue: "درست باشد", // sets the text for "isTrue" radio button
      isFalse: "نادرست باشد",     // sets the text for "isFalse" radio button
      //changes the text of the "And" and "Or" of the filter menu
      and: "و",
      or: "یا",
      selectValue: "-انتخاب کنید-"
      /***************************************************************************/
  });

kendo.ui.FilterMenu.prototype.options.operators =
  $.extend(kendo.ui.FilterMenu.prototype.options.operators, {

      /* FILTER MENU OPERATORS (for each supported data type) 
       ****************************************************************************/
      string: {
          eq: "برابر",
          neq: "مخالف",
          startswith: "شروع می شوند",
          contains: "دارا می باشند",
          doesnotcontain: "دارا نمی باشند",
          endswith: "خاتمه می یابند"
      },
      number: {
          eq: "مساوی",
          neq: "مخالف",
          gte: "بزرگتر یا مساوی",
          gt: "بزگتر",
          lte: "کوچکتر یا مساوی",
          lt: "کوچکتر"
      },
      date: {
          eq: "مساوی",
          neq: "مخالف",
          gte: "بزرگتر یا مساوی",
          gt: "بزگتر",
          lte: "کوچکتر یا مساوی",
          lt: "کوچکتر"
      },
      enums: {
          eq: "برابر",
          neq: "مخالف"
      }
      /***************************************************************************/
  });

kendo.ui.Pager.prototype.options.messages =
  $.extend(kendo.ui.Pager.prototype.options.messages, {

      /* PAGER MESSAGES 
       ****************************************************************************/
      display: "{0} - {1} از {2} مورد",
      empty: "موردی یافت نشد",
      page: "صفحه",
      of: "از {0}",
      itemsPerPage: "تعداد موارد در هر صفحه",
      first: "اولین",
      previous: "قبلی",
      next: "بعدی",
      last: "آخرین",
      refresh: "بازنشانی"
      /***************************************************************************/
  });

kendo.ui.Validator.prototype.options.messages =
  $.extend(kendo.ui.Validator.prototype.options.messages, {

      /* VALIDATOR MESSAGES 
       ****************************************************************************/
      required: "وارد نمودن {0} الزامی است.",
      pattern: "{0} را صحیح وارد نمائید.",
      min: "{0} باید بزرگتر از {1} باشد",
      max: "{0} باید کوچکتر از {1} باشد",
      step: "{0} صحیح نمی باشد.",
      email: "{0} به عنوان آدرس ایمیل صحیح وارد نشده است.",
      url: "{0} به عنوان آدرس اینترنتی صحیح وارد نشده است",
      date: "{0} به عنوان تاریخ صحیح وارد نشده است"
      /***************************************************************************/
  });

kendo.ui.ImageBrowser.prototype.options.messages =
  $.extend(kendo.ui.ImageBrowser.prototype.options.messages, {

      /* IMAGE BROWSER MESSAGES 
       ****************************************************************************/
      uploadFile: "بارگذاری فایل",
      orderBy: "مرتب سازی با",
      orderByName: "مرتب سازی با نام",
      orderBySize: "مرتب سازی با اندازه",
      directoryNotFound: "مسیر مورد نظر یافت نشد.",
      emptyFolder: "خالی نمودن پوشه",
      deleteFile: 'آیا مطمئن هستید که "{0}" پاک شود؟',
      invalidFileType: "فایل انتخاب شده \"{0}\" نامعتبر است. فایل های پشتیبانی شده عبارتند از: {1}.",
      overwriteFile: "فایل با نام \"{0}\" در مسیر مورد نظر وجود دارد. روی آن نوشته شود؟",
      dropFilesHere: "فایل ها را اینجا قرار دهید"
      /***************************************************************************/
  });

kendo.ui.Editor.prototype.options.messages =
  $.extend(kendo.ui.Editor.prototype.options.messages, {

      /* EDITOR MESSAGES 
       ****************************************************************************/
      bold: "پررنگ",
      italic: "مورب",
      underline: "زیرخط",
      strikethrough: "strikethrough",
      superscript: "بالانویس",
      subscript: "زیرنویس",
      justifyCenter: "مرتب سازی به مرکز",
      justifyLeft: "مرتب سازی به چپ",
      justifyRight: "مرتب سازی به راست",
      justifyFull: "مرتب سازی کامل",
      insertUnorderedList: "درج لیست نامرتب",
      insertOrderedList: "درج لیست مرتب",
      indent: "افزایش فاصله",
      outdent: "کاهش فاصله",
      createLink: "ایجاد پیوند",
      unlink: "حذف پیوند",
      insertImage: "درج تصویر",
      insertHtml: "درج HTML",
      fontName: "نام قلم",
      fontNameInherit: "قلم",
      fontSize: "اندازه قلم",
      fontSizeInherit: "اندازه قلم",
      formatBlock: "قالب دهی",
      foreColor: "رنگ",
      backColor: "رنگ پس زمینه",
      style: "طرح",
      emptyFolder: "خالی نمودن مسیر",
      uploadFile: "بارگذاری فایل",
      orderBy: "مرتب سازی با:",
      orderBySize: "مرتب سازی بر اساس اندازه",
      orderByName: "مرتب سازی بر اساس نام",
      invalidFileType: "فایل انتخاب شده\"{0}\" نامعتبر است. فایل های پشتیبانی شده عبارتند از: {1}.",
      deleteFile: 'آیا مطمئن هستید که  "{0}" پاک شود؟',
      overwriteFile: "فایل با نام \"{0}\" در مسیر مورد نظر وجود دارد. روی آن نوشته شود؟",
      directoryNotFound: "مسیر مورد نظر یافت نشد",
      imageWebAddress: "آدرس اینترنتی تصویر",
      imageAltText: "متن جایگزین",
      dialogInsert: "درج",
      dialogButtonSeparator: "یا",
      dialogCancel: "انصراف"
      /***************************************************************************/
  });
