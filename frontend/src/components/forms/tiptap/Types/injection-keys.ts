import type { InjectionKey, Ref } from 'vue'
import type { Editor } from '@tiptap/vue-3'
import type imageItem from './image-item'
import type SelectItem from './select-item'

export const editorKey: InjectionKey<Ref<Editor>> = Symbol()
export const fontFamilyKey: InjectionKey<Ref<SelectItem>> = Symbol()
export const fontFamilyOptionsKey: InjectionKey<Ref<SelectItem[]>> = Symbol()
export const fontSizeKey: InjectionKey<Ref<SelectItem>> = Symbol()
export const fontSizeOptionsKey: InjectionKey<Ref<SelectItem[]>> = Symbol()
export const blockTypeKey: InjectionKey<Ref<SelectItem>> = Symbol()
export const blockTypeOptionsKey: InjectionKey<Ref<SelectItem[]>> = Symbol()
export const imageSelectionsKey: InjectionKey<Ref<imageItem[]>> = Symbol()