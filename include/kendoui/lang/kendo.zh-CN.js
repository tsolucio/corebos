/*
* Kendo UI Localization Project for v2012.3.1114 
* Copyright 2012 Telerik AD. All rights reserved.
* 
* Simplified Chinese (zh-CN) Language Pack
*
* Project home  : https://github.com/loudenvier/kendo-global
* Kendo UI home : http://kendoui.com
* Author        : IKKI Phoenix  
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

kendo.culture("zh-CN"); // Add by IKKI
kendo.ui.Locale = "Simplified Chinese (zh-CN)";

kendo.ui.ColumnMenu.prototype.options.messages = 
	$.extend(kendo.ui.ColumnMenu.prototype.options.messages, {

/* COLUMN MENU MESSAGES 
 ****************************************************************************/   
	sortAscending: "升序排列",
	sortDescending: "降序排列",
	filter: "筛选",
	columns: "字段列"
 /***************************************************************************/   
});

kendo.ui.Groupable.prototype.options.messages = 
	$.extend(kendo.ui.Groupable.prototype.options.messages, {

/* GRID GROUP PANEL MESSAGES 
 ****************************************************************************/   
	empty: "将字段列名称拖拽到此处可进行该列的分组显示"
 /***************************************************************************/   
});

kendo.ui.FilterMenu.prototype.options.messages = 
	$.extend(kendo.ui.FilterMenu.prototype.options.messages, {
  
/* FILTER MENU MESSAGES 
 ***************************************************************************/   
	info: "筛选条件：",	// sets the text on top of the filter menu
	filter: "筛选",		// sets the text for the "Filter" button
	clear: "清空",		// sets the text for the "Clear" button
	// when filtering boolean numbers
	isTrue: "是",		// sets the text for "isTrue" radio button
	isFalse: "否",		// sets the text for "isFalse" radio button
	//changes the text of the "And" and "Or" of the filter menu
	and: "并且",
	or: "或者",
	selectValue: "-= 请选择 =-"
 /***************************************************************************/   
});
         
kendo.ui.FilterMenu.prototype.options.operators =           
	$.extend(kendo.ui.FilterMenu.prototype.options.operators, {

/* FILTER MENU OPERATORS (for each supported data type) 
 ****************************************************************************/   
	string: {
		eq: "等于",
		neq: "不等于",
		contains: "包含",
		doesnotcontain: "不包含",
		startswith: "开始于",
		endswith: "结束于"
	},
	number: {
		eq: "等于",
		neq: "不等于",
		gt: "大于",
		gte: "大于等于",
		lt: "小于",
		lte: "小于等于"
	},
	date: {
		eq: "等于",
		neq: "不等于",
		gt: "晚于",
		gte: "晚于等于",
		lt: "早于",
		lte: "早于等于"
	},
	enums: {
		eq: "等于",
		neq: "不等于"
	}
 /***************************************************************************/   
});

kendo.ui.Pager.prototype.options.messages = 
	$.extend(kendo.ui.Pager.prototype.options.messages, {
  
/* PAGER MESSAGES 
 ****************************************************************************/   
	display: "{0} - {1} 条　共 {2} 条数据",
	empty: "无数据",
	page: "转到第",
	of: "页　共 {0} 页",
	itemsPerPage: "条每页",
	first: "首页",
	previous: "上一页",
	next: "下一页",
	last: "尾页",
	refresh: "刷新"
 /***************************************************************************/   
});

kendo.ui.Validator.prototype.options.messages = 
	$.extend(kendo.ui.Validator.prototype.options.messages, {

/* VALIDATOR MESSAGES 
 ****************************************************************************/   
	required: "{0} 是必填项！",
	pattern: "{0} 的格式不正确！",
	min: "{0} 必须大于或等于 {1} ！",
	max: "{0} 必须小于或等于 {1} ！",
	step: "{0} 不是正确的步进值！",
	email: "{0} 不是正确的电子邮件！",
	url: "{0} 不是正确的网址！",
	date: "{0} 不是正确的日期！"
 /***************************************************************************/   
});

// The upload part add by IKKI
kendo.ui.Upload.prototype.options.localization = 
	$.extend(kendo.ui.Upload.prototype.options.localization, {

/* UPLOAD LOCALIZATION
 ****************************************************************************/   
	select: "选择文件",
	dropFilesHere: "将文件拖拽到此处上传",
	cancel: "取消",
	remove: "移除",
	uploadSelectedFiles: "上传文件",
	statusUploading: "上传中……",
	statusUploaded: "上传成功！",
	statusFailed: "上传失败！",
	retry: "重试"
 /***************************************************************************/   
});

kendo.ui.ImageBrowser.prototype.options.messages = 
	$.extend(kendo.ui.ImageBrowser.prototype.options.messages, {

/* IMAGE BROWSER MESSAGES 
 ****************************************************************************/   
	uploadFile: "上传文件",
	orderBy: "排序方式",
	orderByName: "按名称排序",
	orderBySize: "按大小排序",
	directoryNotFound: "目录未找到",
	emptyFolder: "空文件夹",
	deleteFile: '你确定要删除【{0}】这个文件吗？',
	invalidFileType: "你上传的文件格式 {0} 是无效的，支持的文件类型为：{1}",
	overwriteFile: "一个名为【{0}】的文件已经存在，是否覆盖？",
	dropFilesHere: "将文件拖拽到此处上传"
 /***************************************************************************/   
});

kendo.ui.Editor.prototype.options.messages = 
	$.extend(kendo.ui.Editor.prototype.options.messages, {

/* EDITOR MESSAGES 
 ****************************************************************************/   
	bold: "粗体",
	italic: "斜体",
	underline: "下划线",
	strikethrough: "删除线",
	superscript: "上标",
	subscript: "下标",
	justifyCenter: "居中对齐",
	justifyLeft: "左对齐",
	justifyRight: "右对齐",
	justifyFull: "两端对齐",
	insertUnorderedList: "插入无序列表",
	insertOrderedList: "插入有序列表",
	indent: "增加缩进",
	outdent: "减少缩进",
	createLink: "插入链接",
	unlink: "删除链接",
	insertImage: "插入图片",
	insertHtml: "插入HTML",
	fontName: "请选择字体",
	fontNameInherit: "（默认字体）",
	fontSize: "请选择字号",
	fontSizeInherit: "（默认字号）",
	formatBlock: "格式",
	foreColor: "文字颜色",
	backColor: "文字背景色",
	style: "样式",
	emptyFolder: "空文件夹",
	uploadFile: "上传文件",
	orderBy: "排序方式：",
	orderBySize: "按大小排序",
	orderByName: "按名称排序",
	invalidFileType: "你上传的文件格式 {0} 是无效的，支持的文件类型为：{1}",
	deleteFile: '你确定要删除【{0}】这个文件吗？',
	overwriteFile: '一个名为【{0}】的文件已经存在，是否覆盖？',
	directoryNotFound: "目录未找到",
	imageWebAddress: "图片链接地址",
	imageAltText: "图片占位符",
	linkWebAddress: "链接地址",
	linkText: "链接文字",
	linkToolTip: "文字提示",
	linkOpenInNewWindow: "是否在新窗口中打开",
	dialogInsert: "插入",
	dialogButtonSeparator: "或",
	dialogCancel: "取消"
 /***************************************************************************/   
});
